<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $from;
    protected $isDevMode;

    public function __construct()
    {
        $this->isDevMode = app()->environment('local', 'development');
        
        if (!$this->isDevMode) {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $this->from = config('services.twilio.from');
            
            if (empty($sid) || empty($token)) {
                throw new \Exception('Twilio credentials not configured');
            }
            
            $this->client = new Client($sid, $token);
        }
    }

    public function sendSms($to, $message)
    {
        Log::info('SMS Request:', [
            'to' => $to,
            'message' => $message,
            'dev_mode' => $this->isDevMode
        ]);
        
        // Development mode में actual SMS न भेजें
        if ($this->isDevMode) {
            return $this->sendMockSms($to, $message);
        }
        
        // Production mode
        try {
            // Extract OTP for logging
            $otp = $this->extractOtp($message);
            
            Log::info('Production SMS sending:', [
                'to' => $to,
                'otp' => $otp,
                'from' => $this->from
            ]);
            
            $message = $this->client->messages->create(
                $to,
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );
            
            Log::info('SMS Sent:', [
                'sid' => $message->sid,
                'status' => $message->status
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Twilio Error: ' . $e->getMessage());
            return false;
        }
    }
    
    protected function sendMockSms($to, $message)
    {
        $otp = $this->extractOtp($message);
        
        // Log to file
        $logMessage = date('Y-m-d H:i:s') . " | To: {$to} | OTP: {$otp} | Message: {$message}\n";
        file_put_contents(storage_path('logs/sms_otps.log'), $logMessage, FILE_APPEND);
        
        // Also log to Laravel log
        Log::info('📱 MOCK SMS SENT:', [
            'to' => $to,
            'otp' => $otp,
            'full_message' => $message,
            'log_file' => 'storage/logs/sms_otps.log'
        ]);
        
        // Print to console (helpful for debugging)
        echo "\n\n═══════════════════════════════════════════\n";
        echo "📱 DEVELOPMENT SMS - OTP SENT\n";
        echo "═══════════════════════════════════════════\n";
        echo "To: {$to}\n";
        echo "OTP: {$otp}\n";
        echo "Full Message: {$message}\n";
        echo "Check: storage/logs/sms_otps.log\n";
        echo "═══════════════════════════════════════════\n\n";
        
        return true;
    }
    
    protected function extractOtp($message)
    {
        if (preg_match('/(\d{6})/', $message, $matches)) {
            return $matches[1];
        }
        return 'NOT_FOUND';
    }
}