<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Fast2SmsService
{
    private $apiKey;
    private $baseUrl = 'https://www.fast2sms.com/dev/bulkV2';
    
    public function __construct()
    {
        $this->apiKey = env('FAST2SMS_API_KEY');
        
        if (empty($this->apiKey)) {
            throw new \Exception('Fast2SMS API Key is not configured in .env file');
        }
    }
    
    /**
     * Send OTP to a phone number
     */
    public function sendOtp($phone, $otp, $templateId = null)
    {
        try {
            // Remove any non-numeric characters
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // Ensure it's 10 digits
            if (strlen($phone) !== 10) {
                throw new \Exception('Phone number must be 10 digits');
            }
            
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->baseUrl, [
                'route' => 'otp',
                'variables_values' => $otp,
                'numbers' => $phone,
                'flash' => 0 // 0 for normal SMS, 1 for flash SMS
            ]);
            
            Log::info('Fast2SMS API Response:', [
                'phone' => $phone,
                'otp' => $otp,
                'response' => $response->json(),
                'status' => $response->status()
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['return']) && $result['return'] === true) {
                    return [
                        'success' => true,
                        'message_id' => $result['request_id'] ?? null,
                        'message' => 'OTP sent successfully'
                    ];
                }
            }
            
            return [
                'success' => false,
                'error' => $response->body() ?? 'Unknown error'
            ];
            
        } catch (\Exception $e) {
            Log::error('Fast2SMS Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send normal SMS (not OTP)
     */
    public function sendSms($phone, $message, $senderId = null)
    {
        try {
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            $payload = [
                'route' => 'q',
                'message' => $message,
                'numbers' => $phone,
                'flash' => 0
            ];
            
            if ($senderId) {
                $payload['sender_id'] = $senderId;
            }
            
            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, $payload);
            
            return $response->json();
            
        } catch (\Exception $e) {
            Log::error('Fast2SMS SMS Error: ' . $e->getMessage());
            return ['return' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Check SMS balance
     */
    public function checkBalance()
    {
        try {
            $response = Http::withHeaders([
                'authorization' => $this->apiKey
            ])->get('https://www.fast2sms.com/dev/wallet');
            
            return $response->json();
            
        } catch (\Exception $e) {
            Log::error('Fast2SMS Balance Check Error: ' . $e->getMessage());
            return null;
        }
    }
}