<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;

class PaymentController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    /**
     * Создание платежа
     */
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $amount = $request->input('amount');
        $description = $request->input('description', 'Пополнение баланса');

        try {
            // Создание платежа через YooKassa API
            $payment = $this->client->createPayment([
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => route('payment.callback'),
                ],
                'capture' => true,
                'description' => $description,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ], uniqid('', true));

            // Сохранение платежа в базу данных
            Payment::create([
                'user_id' => $user->id,
                'yookassa_payment_id' => $payment->getId(),
                'amount' => $amount,
                'currency' => 'RUB',
                'status' => 'pending',
                'description' => $description,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            // Перенаправление на страницу оплаты
            return redirect($payment->getConfirmation()->getConfirmationUrl());
        } catch (\Exception $e) {
            Log::error('YooKassa payment creation error: ' . $e->getMessage());
            return back()->with('error', 'Ошибка при создании платежа. Попробуйте позже.');
        }
    }

    /**
     * Callback после оплаты (return_url)
     */
    public function callback(Request $request)
    {
        return redirect()->route('subscriptions')->with('info', 'Проверяем статус вашего платежа...');
    }

    /**
     * Webhook для получения уведомлений от YooKassa
     */
    public function webhook(Request $request)
    {
        try {
            $source = file_get_contents('php://input');
            $data = json_decode($source, true);

            // Проверка типа уведомления
            if (!isset($data['event']) || $data['event'] !== 'payment.succeeded') {
                return response()->json(['status' => 'ignored'], 200);
            }

            $paymentData = $data['object'];
            $paymentId = $paymentData['id'];

            // Поиск платежа в базе данных
            $payment = Payment::where('yookassa_payment_id', $paymentId)->first();

            if (!$payment) {
                Log::warning('Payment not found: ' . $paymentId);
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // Проверка что платеж еще не обработан
            if ($payment->status === 'succeeded') {
                return response()->json(['status' => 'already_processed'], 200);
            }

            // Обновление статуса платежа
            $payment->update([
                'status' => 'succeeded',
                'payment_method' => $paymentData['payment_method']['type'] ?? null,
                'paid_at' => now(),
            ]);

            // Начисление баланса пользователю
            $user = $payment->user;
            $user->balance += $payment->amount;
            $user->save();

            // Создание транзакции
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => 'payment',
                'description' => $payment->description,
            ]);

            Log::info('Payment succeeded: ' . $paymentId . ' for user ' . $user->id);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('YooKassa webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * История платежей пользователя
     */
    public function history()
    {
        $payments = Auth::user()->payments()->latest()->paginate(20);

        return view('payments.history', compact('payments'));
    }

    /**
     * Проверка статуса платежа
     */
    public function status($paymentId)
    {
        try {
            $payment = Payment::where('yookassa_payment_id', $paymentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Получение актуального статуса из YooKassa
            $yooPayment = $this->client->getPaymentInfo($paymentId);

            // Обновление статуса если изменился
            if ($payment->status !== $yooPayment->getStatus()) {
                $payment->update([
                    'status' => $yooPayment->getStatus(),
                    'payment_method' => $yooPayment->getPaymentMethod()?->getType(),
                ]);

                // Если платеж успешен - начислить баланс
                if ($yooPayment->getStatus() === 'succeeded' && !$payment->paid_at) {
                    $user = $payment->user;
                    $user->balance += $payment->amount;
                    $user->save();

                    $payment->update(['paid_at' => now()]);

                    Transaction::create([
                        'user_id' => $user->id,
                        'amount' => $payment->amount,
                        'type' => 'payment',
                        'description' => $payment->description,
                    ]);
                }
            }

            return response()->json([
                'status' => $payment->status,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment status check error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment not found'], 404);
        }
    }
}
