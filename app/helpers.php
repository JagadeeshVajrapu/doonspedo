<?php

if (!function_exists('currency_symbol')) {
    /**
     * Return the default currency symbol from DB.
     * Falls back to '₹' if none is set as default.
     */
    function currency_symbol(): string
    {
        static $symbol = null;
        if ($symbol === null) {
            try {
                $currency = \App\Models\Currency::where('is_default', true)->first()
                         ?? \App\Models\Currency::where('is_active', true)->first();
                $symbol = $currency ? $currency->symbol : '₹';
            } catch (\Exception $e) {
                $symbol = '₹';
            }
        }
        return $symbol;
    }
}

if (!function_exists('format_price')) {
    /**
     * Format a number with the default currency symbol.
     * Usage: {{ format_price(200.50) }}  → ₹200.50
     */
    function format_price(float $amount, int $decimals = 2): string
    {
        return currency_symbol() . number_format($amount, $decimals);
    }
}

if (!function_exists('send_sms')) {
    /**
     * Send SMS using configured provider (TrueBulkSMS).
     */
    function send_sms($mobile, $message, $tempId = null)
    {
        try {
            $settingsFile = storage_path('app/settings.json');
            if (!file_exists($settingsFile)) return false;
            
            $settings = json_decode(file_get_contents($settingsFile), true);
            $provider = $settings['sms_provider'] ?? 'none';

            if ($provider == 'truebulksms') {
                $user = $settings['true_bulk_sms_username'] ?? '';
                $pass = $settings['true_bulk_sms_password'] ?? '';
                $sender = $settings['true_bulk_sms_sender_id'] ?? '';
                $tempId = $tempId ?? ($settings['true_bulk_sms_temp_id'] ?? '');
                
                // Construct TrueBulkSMS API URL (Correct .biz domain)
                $url = "http://truebulksms.biz/api.php";
                
                // Ensure mobile number has 91 prefix if it's 10 digits
                $mobile = preg_replace('/[^0-9]/', '', $mobile);
                if (strlen($mobile) == 10) {
                    $mobile = '91' . $mobile;
                }

                $response = \Illuminate\Support\Facades\Http::get($url, [
                    'username'   => $user,
                    'password'   => $pass,
                    'sender'     => $sender,
                    'sendto'     => $mobile,
                    'message'    => $message,
                    'templateid' => $tempId,
                    'PEID'       => $settings['true_bulk_sms_entity_id'] ?? '',
                ]);

                $body = $response->body();
                \Illuminate\Support\Facades\Log::info("TrueBulkSMS Response: " . $body);

                if (!$response->successful()) {
                    \Illuminate\Support\Facades\Log::error('TrueBulkSMS Connection Failed: ' . $body);
                    return false;
                }

                // TrueBulkSMS successful responses often start with "Success" or a numeric ID
                if (strpos(strtolower($body), 'success') !== false || is_numeric(trim($body)) || strlen(trim($body)) > 10) {
                    return true;
                }

                return false;

            }

            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SMS Sending Failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_settings')) {
    /**
     * Get a setting value by key from the settings.json file.
     */
    function get_settings($key, $default = null)
    {
        static $settings = null;
        if ($settings === null) {
            $path = storage_path('app/settings.json');
            $settings = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        }
        return $settings[$key] ?? $default;
    }
}
