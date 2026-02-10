<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\VideoCall;
use App\Services\AgoraService;
use App\Services\FirebaseService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

class VideoCallController extends Controller
{
    protected $agoraService;

     protected $appId;
    protected $appCertificate;

    // Role constants
    const ROLE_PUBLISHER = 1;    // Can publish stream
    const ROLE_SUBSCRIBER = 2;

    public function __construct()
    {
        $this->agoraService = new AgoraService();
         $this->appId = config('services.agora.key');
        $this->appCertificate = config('services.agora.secret');
    }

    


    public function getActiveCall(Request $request)
    {

        try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update profile.'
                ], 401);
            }
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }


        $appointmentId = $request->appointment_id;

        // Check if appointment belongs to patient
        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $user->patient->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment not found or access denied'
            ], 404);
        }

        $callDetails = $this->agoraService->getPatientCallDetails($appointmentId);

        if (!$callDetails) {
            return response()->json([
                'status' => false,
                'message' => 'No active call found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Call details retrieved',
            'data' => $callDetails
        ]);
    }


    public function joinCall(Request $request)
    {
        try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update profile.'
                ], 401);
            }

        $validator = Validator::make($request->all(), [
            'call_id' => 'required|exists:video_calls,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $callId = $request->call_id;

        $videoCall = VideoCall::find($callId);

        // Check if appointment belongs to patient
        $appointment = $videoCall->appointment;
        if ($appointment->patient_id !== $user->patient->id) {
            return response()->json([
                'status' => false,
                'message' => 'Access denied to this call'
            ], 403);
        }

        // Generate token for patient
        // $token = $this->agoraService->generateToken(
        //     $videoCall->channel_name,
        //     $user->id, 
        //     2, 
        //     3600
        // );

        $token = $this->generateToken($videoCall->channel_name, $user->id);

        // Update call status
        $videoCall->update([
            'status' => 'ongoing'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Call joined successfully',
            'data' => [
                'channel_name' => $videoCall->channel_name,
                'token' => $token,
                'uid' => $user->id,
                'app_id' => config('services.agora.key'),
                'call_id' => $videoCall->id,
                'appointment_id' => $videoCall->appointment_id
            ]
        ]);
    }

    public function generateToken($channelName, $uid)
    {
        $expireTimeInSeconds = 3600;
        
        $appID = config('services.agora.key');
        $appCertificate = config('services.agora.secret');
        
        $privilegeExpiredTs = time() + $expireTimeInSeconds;

        // Use the new package's RtcTokenBuilder
        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            RtcTokenBuilder::RolePublisher,
            $privilegeExpiredTs
        );

        return $token;
    }


    public function startCall(Request $request)
    {
        try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update profile.'
                ], 401);
            }
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $appointmentId = $request->appointment_id;

        // Check if doctor owns this appointment
        $appointment = Appointment::where('id', $appointmentId)
            ->where('doctor_id', $user->doctor->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment not found or access denied'
            ], 404);
        }

        // Create video call
        $videoCall = $this->agoraService->createVideoCall($appointmentId, $user->id);

        // Generate token for doctor
        $token = $videoCall->token;

        return response()->json([
            'status' => true,
            'message' => 'Call started successfully',
            'data' => [
                'channel_name' => $videoCall->channel_name,
                'token' => $token,
                'uid' => $user->id,
                'app_id' => config('services.agora.key'),
                'call_id' => $videoCall->id,
                'appointment_id' => $videoCall->appointment_id
            ]
        ]);
    }

    public function endCall(Request $request)
    {
        try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update profile.'
                ], 401);
            }
        $validator = Validator::make($request->all(), [
            'call_id' => 'required|exists:video_calls,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }


        $callId = $request->call_id;

        $videoCall = VideoCall::find($callId);
        $appointment = $videoCall->appointment;

        // Check if user has access to this call
        if ($user->user_type === 'doctor') {
            if ($appointment->doctor_id !== $user->doctor->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied'
                ], 403);
            }
        } else if ($user->user_type === 'patient') {
            if ($appointment->patient_id !== $user->patient->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied'
                ], 403);
            }
        }

        // Calculate duration
        $startedAt = $videoCall->started_at;
        $endedAt = now();
        $duration = $startedAt ? $endedAt->diffInSeconds($startedAt) : 0;

        // Update call
        $videoCall->update([
            'status' => 'completed',
            'ended_at' => $endedAt,
            'duration' => $duration
        ]);

        // Notify the other participant (if possible) and persist a Notification
        try {
            $otherUser = null;
            if ($user->user_type === 'doctor') {
                $otherUser = $appointment->patient->user ?? null;
            } else if ($user->user_type === 'patient') {
                $otherUser = $appointment->doctor->user ?? null;
            }

            if ($otherUser) {
                $projectId = config('services.firebase.project_id');
                $credentialsPath = public_path(config('services.firebase.credentials_path'));

                try {
                    if ($otherUser->device_token) {
                        $fcm = new FirebaseService($projectId, $credentialsPath);
                        $fcm->sendNotification([$otherUser->device_token], [
                            'title' => 'Video Consultation Ended',
                            'body' => ($user->user_type === 'doctor') ? 'Doctor ended the call' : 'Patient left the call',
                            'call_id' => $videoCall->id,
                            'appointment_id' => $videoCall->appointment_id,
                            'type' => 'video_call_ended'
                        ]);
                    }
                } catch (\Throwable $e) {
                    // fallback helper
                    if (!empty($otherUser->device_token)) {
                        \App\Helpers\FirebaseNotification::send($otherUser->device_token, 'Video Consultation Ended', ($user->user_type === 'doctor') ? 'Doctor ended the call' : 'Patient left the call', ['call_id' => $videoCall->id, 'appointment_id' => $videoCall->appointment_id, 'type' => 'video_call_ended']);
                    }
                }

                \App\Models\Notification::create([
                    'user_id' => $otherUser->id,
                    'type' => 'video_call_ended',
                    'title' => 'Video Consultation Ended',
                    'meta_data' => json_encode(['call_id' => $videoCall->id, 'appointment_id' => $videoCall->appointment_id]),
                    'sender_id' => $user->id,
                ]);
            }
        } catch (\Throwable $e) {
            logger()->error('Error notifying other participant on call end: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Call ended successfully',
            'data' => [
                'duration' => $duration,
                'ended_at' => $endedAt->toDateTimeString()
            ]
        ]);
    }


    public function getCallStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'call_id' => 'required|exists:video_calls,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $call = VideoCall::find($request->call_id);

        return response()->json([
            'status' => true,
            'data' => [
                'call_id' => $call->id,
                'status' => $call->status,
                'ended_at' => $call->ended_at,
                'appointment_id' => $call->appointment_id
            ]
        ]);
    }


    public function callHistory(Request $request)
    {
        try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first to update profile.'
                ], 401);
            }
        $limit = $request->get('limit', 10);

        if ($user->user_type === 'patient') {
            $appointments = $user->patient->appointments()->pluck('id');
        } else if ($user->user_type === 'doctor') {
            $appointments = $user->doctor->appointments()->pluck('id');
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user type'
            ], 400);
        }

        $calls = VideoCall::whereIn('appointment_id', $appointments)
            ->with(['appointment', 'appointment.patient', 'appointment.doctor'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'Call history retrieved',
            'data' => $calls
        ]);
    }
}