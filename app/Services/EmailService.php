<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send verification code email.
     */
    public function sendVerificationCode(string $toEmail, string $code): bool
    {
        try {
            Mail::send([], [], function ($message) use ($toEmail, $code) {
                $message->to($toEmail)
                    ->subject('Подтверждение email - Business database')
                    ->html($this->getVerificationEmailHtml($code), 'text/html');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password email after registration.
     */
    public function sendPasswordEmail(string $toEmail, string $username, string $password): bool
    {
        try {
            Mail::send([], [], function ($message) use ($toEmail, $username, $password) {
                $message->to($toEmail)
                    ->subject('Спасибо за регистрацию - Business database')
                    ->html($this->getPasswordEmailHtml($username, $password), 'text/html');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate verification code HTML.
     */
    private function getVerificationEmailHtml(string $code): string
    {
        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset=\"utf-8\">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { background-color: #f8f9fa; padding: 30px; margin: 20px 0; }
                .code { font-size: 32px; font-weight: bold; color: #007bff; text-align: center; padding: 20px; background-color: white; border: 2px dashed #007bff; letter-spacing: 5px; }
                .footer { text-align: center; color: #6c757d; font-size: 12px; padding: 20px; }
            </style>
        </head>
        <body>
            <div class=\"container\">
                <div class=\"header\"><h1>Business database - Подтверждение Email</h1></div>
                <div class=\"content\">
                    <p>Здравствуйте!</p>
                    <p>Для завершения регистрации на платформе Business database введите этот код подтверждения:</p>
                    <div class=\"code\">{$code}</div>
                    <p><strong>Код действителен в течение 10 минут.</strong></p>
                    <p>Если вы не регистрировались на Business database, просто проигнорируйте это письмо.</p>
                </div>
                <div class=\"footer\">
                    <p>С уважением,<br>Команда Business database</p>
                    <p>Это автоматическое письмо, не отвечайте на него.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Generate password email HTML.
     */
    private function getPasswordEmailHtml(string $username, string $password): string
    {
        $loginUrl = config('app.url') . '/login';

        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset=\"utf-8\">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #212529; margin: 0; padding: 0; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background-color: #0d6efd; padding: 30px 20px; text-align: center; }
                .content { padding: 40px 30px; }
                .greeting { font-size: 24px; font-weight: 600; color: #212529; margin-bottom: 20px; }
                .message { font-size: 16px; color: #495057; margin-bottom: 30px; }
                .credentials { background-color: #f8f9fa; border-left: 4px solid #0d6efd; padding: 20px; margin: 30px 0; }
                .credentials-label { font-size: 14px; color: #6c757d; margin-bottom: 5px; }
                .credentials-value { font-size: 18px; font-weight: 600; color: #0d6efd; font-family: 'Courier New', monospace; word-break: break-all; }
                .footer { text-align: center; padding: 30px; background-color: #f8f9fa; color: #6c757d; font-size: 14px; }
                .btn { display: inline-block; padding: 12px 30px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: 500; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class=\"container\">
                <div class=\"header\"><h1 style=\"color: white; margin: 0;\">Business database</h1></div>
                <div class=\"content\">
                    <div class=\"greeting\">Спасибо за регистрацию!</div>
                    <div class=\"message\">Здравствуйте, <strong>{$username}</strong>!<br>Ваш аккаунт в Business database успешно создан.</div>
                    <div class=\"credentials\">
                        <div class=\"credentials-label\">Ваш логин:</div>
                        <div class=\"credentials-value\">{$username}</div>
                    </div>
                    <div class=\"credentials\">
                        <div class=\"credentials-label\">Ваш пароль:</div>
                        <div class=\"credentials-value\">{$password}</div>
                    </div>
                    <div class=\"message\">Используйте эти данные для входа в систему.<br>Рекомендуем изменить пароль после первого входа в настройках профиля.</div>
                    <center><a href=\"{$loginUrl}\" class=\"btn\">Войти в систему</a></center>
                </div>
                <div class=\"footer\">
                    <p>С уважением,<br>Команда Business database</p>
                    <p>Это автоматическое письмо, не отвечайте на него.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Generate strong password.
     */
    public static function generatePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $special = '!@#$%^&*';

        // Ensure at least one character from each set
        $password = [
            $uppercase[random_int(0, strlen($uppercase) - 1)],
            $lowercase[random_int(0, strlen($lowercase) - 1)],
            $digits[random_int(0, strlen($digits) - 1)],
            $special[random_int(0, strlen($special) - 1)],
        ];

        // Fill the rest with random characters
        $allChars = $uppercase . $lowercase . $digits . $special;
        for ($i = 4; $i < $length; $i++) {
            $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        shuffle($password);

        return implode('', $password);
    }
}
