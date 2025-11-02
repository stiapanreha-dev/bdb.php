<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification view.
     */
    public function show()
    {
        $user = Auth::user();

        if ($user->email_verified) {
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    }

    /**
     * Verify the email with the provided code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        // Find the verification code
        $verification = EmailVerification::where('user_id', $user->id)
            ->where('verification_type', 'email')
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

        // Mark email AND phone as verified (automatic phone verification like Flask)
        $user->email_verified = true;
        $user->phone_verified = true;
        $user->save();

        return redirect()->route('home')
            ->with('success', 'Email успешно подтвержден! Добро пожаловать!');
    }

    /**
     * Resend verification code.
     */
    public function resend()
    {
        $user = Auth::user();

        if ($user->email_verified) {
            return back()->with('warning', 'Email уже подтвержден.');
        }

        // Invalidate old codes
        EmailVerification::where('user_id', $user->id)
            ->where('verification_type', 'email')
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Create new verification code
        $code = EmailVerification::generateCode();
        EmailVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'verification_type' => 'email',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send email
        $emailService = new EmailService();
        $emailService->sendVerificationCode($user->email, $code);

        return redirect()->route('verification.email')
            ->with('success', 'Новый код отправлен на ваш email. Введите код из письма для подтверждения.');
    }
}
