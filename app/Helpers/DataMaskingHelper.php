<?php

namespace App\Helpers;

class DataMaskingHelper
{
    /**
     * Mask email address for unpaid users.
     * Example: example@domain.com → exa***@dom***
     *
     * @param string|null $email
     * @return string
     */
    public static function maskEmail(?string $email): string
    {
        if (empty($email)) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        [$local, $domain] = $parts;

        // Mask local part (show first 3 chars)
        $localMasked = strlen($local) > 3
            ? substr($local, 0, 3) . '***'
            : $local;

        // Mask domain (show first 3 chars before TLD)
        $domainParts = explode('.', $domain);
        if (count($domainParts) > 1) {
            $domainName = $domainParts[0];
            $domainNameMasked = strlen($domainName) > 3
                ? substr($domainName, 0, 3) . '***'
                : $domainName;
            $domainMasked = $domainNameMasked . '.' . implode('.', array_slice($domainParts, 1));
        } else {
            $domainMasked = strlen($domain) > 3
                ? substr($domain, 0, 3) . '***'
                : $domain;
        }

        return $localMasked . '@' . $domainMasked;
    }

    /**
     * Mask phone number for unpaid users.
     * Example: +7 (123) 456-78-90 → +7 (123) ***-**-90
     *
     * @param string|null $phone
     * @return string
     */
    public static function maskPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Extract only digits
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) < 4) {
            return $phone;
        }

        // Keep first 4 and last 2 digits, mask the rest
        $visibleStart = substr($digits, 0, 4);
        $visibleEnd = substr($digits, -2);
        $maskedLength = strlen($digits) - 6;

        if ($maskedLength > 0) {
            $masked = $visibleStart . str_repeat('*', $maskedLength) . $visibleEnd;
        } else {
            $masked = $digits;
        }

        // Try to preserve original format
        if (str_starts_with($phone, '+7')) {
            return '+7 (' . substr($masked, 1, 3) . ') ***-**-' . $visibleEnd;
        } elseif (str_starts_with($phone, '8')) {
            return '8 (' . substr($masked, 1, 3) . ') ***-**-' . $visibleEnd;
        } else {
            return $masked;
        }
    }

    /**
     * Mask website URL for unpaid users.
     * Example: https://example.com/path → https://exa***.com
     *
     * @param string|null $site
     * @return string
     */
    public static function maskSite(?string $site): string
    {
        if (empty($site)) {
            return '';
        }

        // Extract domain from URL
        $parsed = parse_url($site);
        if (!isset($parsed['host'])) {
            // If not a valid URL, treat as domain
            $domain = $site;
        } else {
            $domain = $parsed['host'];
        }

        // Remove www. prefix
        $domain = preg_replace('/^www\./i', '', $domain);

        // Mask domain
        $domainParts = explode('.', $domain);
        if (count($domainParts) > 1) {
            $domainName = $domainParts[0];
            $domainNameMasked = strlen($domainName) > 3
                ? substr($domainName, 0, 3) . '***'
                : $domainName;
            $domainMasked = $domainNameMasked . '.' . implode('.', array_slice($domainParts, 1));
        } else {
            $domainMasked = strlen($domain) > 3
                ? substr($domain, 0, 3) . '***'
                : $domain;
        }

        // Reconstruct with protocol if present
        if (isset($parsed['scheme'])) {
            return $parsed['scheme'] . '://' . $domainMasked;
        }

        return $domainMasked;
    }

    /**
     * Apply masking to array of records based on user's balance.
     *
     * @param array $records Array of database records
     * @param bool $hasBalance Whether user has positive balance
     * @param array $fieldMap Field mapping: ['email' => 'email_field', 'phone' => 'phone_field', 'site' => 'site_field']
     * @return array
     */
    public static function applyMasking(array $records, bool $hasBalance, array $fieldMap = []): array
    {
        if ($hasBalance) {
            return $records; // No masking for paid users
        }

        $defaultFieldMap = [
            'email' => 'email',
            'phone' => 'phone',
            'site' => 'site',
        ];

        $fieldMap = array_merge($defaultFieldMap, $fieldMap);

        foreach ($records as &$record) {
            if (isset($record[$fieldMap['email']])) {
                $record[$fieldMap['email']] = self::maskEmail($record[$fieldMap['email']]);
            }
            if (isset($record[$fieldMap['phone']])) {
                $record[$fieldMap['phone']] = self::maskPhone($record[$fieldMap['phone']]);
            }
            if (isset($record[$fieldMap['site']])) {
                $record[$fieldMap['site']] = self::maskSite($record[$fieldMap['site']]);
            }
        }

        return $records;
    }
}
