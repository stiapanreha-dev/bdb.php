<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display available tariffs for subscription
     */
    public function index()
    {
        $tariffs = Tariff::active()->orderBy('duration_days')->get();
        $activeSubscription = auth()->check() ? auth()->user()->activeSubscription : null;

        return view('subscriptions.index', compact('tariffs', 'activeSubscription'));
    }

    /**
     * Purchase a tariff subscription
     */
    public function subscribe(Request $request, Tariff $tariff)
    {
        $user = auth()->user();

        if (!$tariff->is_active) {
            return back()->with('error', 'Этот тариф недоступен');
        }

        // Проверяем баланс
        if ($user->balance < $tariff->price) {
            return back()->with('error', 'Недостаточно средств на балансе. Необходимо: ' . $tariff->price . ' руб');
        }

        DB::beginTransaction();
        try {
            // Получаем текущую активную подписку
            $currentSubscription = $user->activeSubscription;

            // Определяем дату начала новой подписки
            if ($currentSubscription) {
                // Если есть активная подписка, новая начинается после её окончания
                $startsAt = $currentSubscription->expires_at;
            } else {
                // Если нет активной подписки, начинаем сейчас
                $startsAt = now();
            }

            $expiresAt = $startsAt->copy()->addDays($tariff->duration_days);

            // Создаем подписку
            UserSubscription::create([
                'user_id' => $user->id,
                'tariff_id' => $tariff->id,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'is_active' => true,
                'paid_amount' => $tariff->price,
            ]);

            // Списываем средства с баланса
            $user->decrement('balance', $tariff->price);

            DB::commit();

            $message = 'Подписка успешно оформлена! ';
            if ($currentSubscription) {
                $message .= 'Новый тариф начнет действовать ' . $startsAt->format('d.m.Y H:i');
            } else {
                $message .= 'Тариф активирован и действует до ' . $expiresAt->format('d.m.Y H:i');
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при оформлении подписки: ' . $e->getMessage());
        }
    }

    /**
     * Show subscription history
     */
    public function history()
    {
        $subscriptions = auth()->user()
            ->subscriptions()
            ->with('tariff')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('subscriptions.history', compact('subscriptions'));
    }
}
