<?php

namespace App\Services;

use App\Models\User;
use App\Models\VideoCall;
use Illuminate\Support\Facades\Log;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

class AgoraService
{
    protected $appId;
    protected $appCertificate;

    // Role constants
    const ROLE_PUBLISHER = 1;    // Can publish stream
    const ROLE_SUBSCRIBER = 2;   // Can only subscribe

    public function __construct()
    {
        $this->appId = config('services.agora.key');
        $this->appCertificate = config('services.agora.secret');
    }


    public function generateToken($channelName, $uid, $role = self::ROLE_PUBLISHER, $expireTimeInSeconds = 3600)
    {
        $appID = $this->appId;
        $appCertificate = $this->appCertificate;
        $privilegeExpiredTs = time() + $expireTimeInSeconds;

        // Use the new package's RtcTokenBuilder
        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs
        );

        return $token;
    }


    public function createVideoCall($appointmentId, $userId)
    {
        // Try to reuse an existing active call (initiated or ongoing)
        // Use a DB transaction + row lock to avoid race conditions that create duplicate calls
        return \DB::transaction(function () use ($appointmentId, $userId) {
            $existing = VideoCall::where('appointment_id', $appointmentId)
                ->whereIn('status', ['initiated', 'ongoing'])
                ->latest()
                ->lockForUpdate()
                ->first();

            if ($existing) {
                // Optionally we could refresh or re-generate token here if tokens are time-limited
                return $existing;
            }

            $channelName = 'appointment_' . $appointmentId;
            $token = $this->generateToken($channelName, $userId, self::ROLE_PUBLISHER);

            $videoCall = VideoCall::create([
                'appointment_id' => $appointmentId,
                'channel_name' => $channelName,
                'token' => $token,
                'status' => 'initiated',
                'started_at' => now(),
            ]);

            return $videoCall;
        });
    }


    public function getPatientCallDetails($appointmentId)
    {
        $videoCall = VideoCall::where('appointment_id', $appointmentId)
            ->whereIn('status', ['initiated', 'ongoing'])
            ->latest()
            ->first();

        if (!$videoCall) {
            return null;
        }

        return [
            'channel_name' => $videoCall->channel_name,
            'token' => $videoCall->token,
            'status' => $videoCall->status,
            'started_at' => $videoCall->started_at,
            'appointment_id' => $videoCall->appointment_id,
            'call_id' => $videoCall->id
        ];
    }


    public function generateTokenWithUserAccount($channelName, $userAccount, $role = self::ROLE_PUBLISHER, $expireTimeInSeconds = 3600)
    {
        $appID = $this->appId;
        $appCertificate = $this->appCertificate;
        $privilegeExpiredTs = time() + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUserAccount(
            $appID,
            $appCertificate,
            $channelName,
            $userAccount,
            $role,
            $privilegeExpiredTs
        );

        return $token;
    }
}
