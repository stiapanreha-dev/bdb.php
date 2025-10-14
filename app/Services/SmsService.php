<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiKey;
    private string $apiUrl;
    private string $fromName;

    public function __construct()
    {
        $this->apiKey = config('services.smsru.api_key', '');
        $this->apiUrl = 'https://sms.ru/sms/send';
        $this->fromName = config('services.smsru.from_name', '');
    }

    /**
     * Send SMS via SMS.ru API.
     */
    public function sendSms(string $phone, string $text): bool
    {
        if (empty($this->apiKey)) {
            Log::error('SMSRU_API_KEY not configured');
            return false;
        }

        // Clean phone number - only digits
        $phoneClean = preg_replace('/\D/', '', $phone);

        // Replace leading 8 with 7
        if (str_starts_with($phoneClean, '8')) {
            $phoneClean = '7' . substr($phoneClean, 1);
        }

        // Add 7 if doesn't start with 7
        if (!str_starts_with($phoneClean, '7')) {
            $phoneClean = '7' . $phoneClean;
        }

        try {
            $params = [
                'api_id' => $this->apiKey,
                'to' => $phoneClean,
                'msg' => $text,
                'json' => 1
            ];

            // Add from name if configured
            if (!empty($this->fromName)) {
                $params['from'] = $this->fromName;
            }

            $response = Http::timeout(10)->get($this->apiUrl, $params);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'OK') {
                    Log::info("SMS sent successfully to {$phoneClean}");
                    return true;
                } else {
                    $errorCode = $data['status_code'] ?? 'unknown';
                    $errorText = $data['status_text'] ?? 'неизвестная ошибка';
                    Log::error("SMS.ru error: {$errorCode} - {$errorText}");
                    return false;
                }
            } else {
                Log::error("HTTP error: " . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send verification code via SMS.
     */
    public function sendVerificationCode(string $phone, string $code): bool
    {
        $text = "Ваш код подтверждения Business database: {$code}\n\nКод действителен 10 минут.";
        return $this->sendSms($phone, $text);
    }
}
