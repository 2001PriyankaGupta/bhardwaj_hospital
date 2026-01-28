<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotification
{
    public static function send($deviceToken, $title, $body, $data = [])
    {
        if (!$deviceToken) return false;

        $serverKey = env('FIREBASE_SERVER_KEY');
        if (!$serverKey) {
            Log::warning('Firebase server key (FIREBASE_SERVER_KEY) not set - FCM notifications disabled.');
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json'
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body'  => $body,
                'sound' => 'default'
            ],
            'data' => $data
        ]);

        Log::info('FCM Response', $response->json());

        return $response->successful();
    }
}
