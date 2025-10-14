<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhoneVerificationController extends Controller
{
    /**
     * Display the phone verification view.
     */
    public function show()
    {
        $user = Auth::user();

        if (!$user->email_verified) {
            return redirect()->route('verification.email')
                ->with('warning', 'Сначала подтвердите email.');
        }

        if ($user->phone_verified) {
            return redirect()->route('dashboard')
                ->with('success', 'Верификация завершена!');
        }

        return view('auth.verify-phone');
    }

    /**
     * Verify the phone with the provided code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        // Find the verification code
        $verification = EmailVerification::where('user_id', $user->id)
            ->where('verification_type', 'phone')
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$verification) {
            return back()->withErrors(['code' => 'Код верификации не найден или истёк. Запросите новый код.']);
        }

        if ($verification->code !== $request->code) {
            return back()->withErrors(['code' => 'Неверный код верификации.']);
        }

        // Mark code as used
        $verification->is_used = true;
        $verification->save();

        // Mark phone as verified
        $user->phone_verified = true;
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Номер телефона успешно подтвержден! Добро пожаловать!');
    }

    /**
     * Resend verification code.
     */
    public function resend()
    {
        $user = Auth::user();

        if ($user->phone_verified) {
            return back()->with('warning', 'Телефон уже подтвержден.');
        }

        // Invalidate old codes
        EmailVerification::where('user_id', $user->id)
            ->where('verification_type', 'phone')
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Create new verification code
        $code = EmailVerification::generateCode();
        EmailVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'verification_type' => 'phone',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send SMS
        $smsService = new SmsService();
        $smsService->sendVerificationCode($user->phone, $code);

        return back()->with('success', 'Новый код отправлен на ваш телефон.');
    }

    /**
     * Send initial phone verification code.
     */
    public function send()
    {
        $user = Auth::user();

        if ($user->phone_verified) {
            return back()->with('warning', 'Телефон уже подтвержден.');
        }

        // Create verification code
        $code = EmailVerification::generateCode();
        EmailVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'verification_type' => 'phone',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send SMS
        $smsService = new SmsService();
        $sent = $smsService->sendVerificationCode($user->phone, $code);

        if ($sent) {
            return back()->with('success', 'Код отправлен на ваш телефон.');
        } else {
            return back()->with('error', 'Ошибка отправки SMS. Попробуйте позже.');
        }
    }
}
