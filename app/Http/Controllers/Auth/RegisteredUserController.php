<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use App\Services\EmailService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Auto-generate strong password
        $password = EmailService::generatePassword();

        $user = User::create([
            'name' => $request->username, // Use username as name
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($password),
            'role' => 'user',
            'balance' => 0.00,
            'email_verified' => false,
            'phone_verified' => false,
        ]);

        // Send password email
        $emailService = new EmailService();
        $passwordEmailSent = $emailService->sendPasswordEmail($user->email, $user->name, $password);

        // Create email verification code
        $emailCode = EmailVerification::generateCode();
        EmailVerification::create([
            'user_id' => $user->id,
            'code' => $emailCode,
            'verification_type' => 'email',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send email verification code
        $verificationEmailSent = $emailService->sendVerificationCode($user->email, $emailCode);

        event(new Registered($user));

        // Login user but redirect to email verification
        Auth::login($user);

        // Prepare success message with warnings if emails failed
        $message = 'Регистрация успешна!';
        if (!$passwordEmailSent || !$verificationEmailSent) {
            $message .= ' Внимание: возникли проблемы с отправкой email. ';
            if (!$passwordEmailSent) {
                $message .= 'Ваш пароль: ' . $password . '. Сохраните его! ';
            }
            if (!$verificationEmailSent) {
                $message .= 'Код подтверждения: ' . $emailCode . '.';
            }
        } else {
            $message .= ' Проверьте ваш email для подтверждения.';
        }

        return redirect()->route('verification.email')
            ->with('success', $message);
    }
}
