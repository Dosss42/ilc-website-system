<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    /**
     * Verify Google reCAPTCHA v2 response
     *
     * @param string|null $token The g-recaptcha-response token
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function verify(?string $token): array
    {
        // If token is null or empty, verification fails
        if (empty($token)) {
            return ['success' => false, 'error' => 'Please check the "I\'m not a robot" box.'];
        }

        $secretKey = config('services.recaptcha.secret_key');

        // Skip verification if no secret key is set (development mode)
        if ($secretKey === 'your_secret_key_here' || empty($secretKey)) {
            return ['success' => true, 'error' => null];
        }

        try {
            $response = Http::timeout(30)
                ->withoutVerifying() // Skip SSL verification for local dev
                ->retry(3, 1000) // Retry 3 times with 1 second delay
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            // Log the raw response for debugging
            $responseBody = $response->body();
            \Log::info('reCAPTCHA raw response', ['body' => $responseBody]);

            if ($response->successful()) {
                $data = $response->json();
                $success = $data['success'] ?? false;
                
                if (!$success) {
                    $errorCodes = $data['error-codes'] ?? [];
                    $errorMessage = self::getErrorMessage($errorCodes);
                    \Log::warning('reCAPTCHA validation failed', [
                        'error-codes' => $errorCodes,
                        'ip' => request()->ip(),
                        'response' => $data
                    ]);
                    return ['success' => false, 'error' => $errorMessage];
                }
                
                return ['success' => true, 'error' => null];
            } else {
                \Log::error('reCAPTCHA HTTP request failed', [
                    'status' => $response->status(),
                    'body' => $responseBody
                ]);
                return ['success' => false, 'error' => 'Verification service error (HTTP ' . $response->status() . '). Please try again.'];
            }
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            // For local development, allow login on connection errors
            if (app()->environment('local', 'development')) {
                \Log::warning('reCAPTCHA connection failed in local dev - allowing login');
                return ['success' => true, 'error' => null];
            }
            
            return ['success' => false, 'error' => 'Verification service temporarily unavailable. Please try again later.'];
        }

        return ['success' => false, 'error' => 'Verification failed. Please try again.'];
    }

    /**
     * Get human-readable error message from reCAPTCHA error codes
     */
    private static function getErrorMessage(array $errorCodes): string
    {
        $messages = [
            'timeout-or-duplicate' => 'The verification expired. Please check the box again.',
            'invalid-input-secret' => 'Invalid reCAPTCHA configuration.',
            'invalid-input-response' => 'Invalid verification response. Please try again.',
            'bad-request' => 'Invalid request. Please refresh the page and try again.',
            'hostname-mismatch' => 'Domain verification failed. Please contact support.',
        ];

        foreach ($errorCodes as $code) {
            if (isset($messages[$code])) {
                return $messages[$code];
            }
        }

        return 'Verification failed. Please try again.';
    }

    /**
     * Quick verify - returns bool only
     */
    public static function verifyBool(?string $token): bool
    {
        $result = self::verify($token);
        return $result['success'];
    }

    /**
     * Check if reCAPTCHA is enabled
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        $siteKey = config('services.recaptcha.site_key');
        return !empty($siteKey) && $siteKey !== 'your_site_key_here';
    }
}
