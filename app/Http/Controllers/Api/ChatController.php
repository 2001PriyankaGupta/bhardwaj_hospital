<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatQuickReply;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;


class ChatController extends Controller
{
    private function getFCM()
    {
        $projectId = config('services.firebase.project_id');
        $credentialsConfig = config('services.firebase.credentials_path');
        $credentialsPath = $credentialsConfig ? public_path($credentialsConfig) : null;

        if ($projectId && $credentialsPath && file_exists($credentialsPath)) {
            try {
                return new \App\Services\FirebaseService($projectId, $credentialsPath);
            } catch (\Throwable $e) {
                logger()->error('FirebaseService init failed in ChatController: ' . $e->getMessage());
                return null;
            }
        } else {
            logger()->warning('Firebase not configured in ChatController: missing service account JSON or FIREBASE_PROJECT_ID.');
            return null;
        }
    }

    // List all patient conversations (paginated)
    public function listConversations(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access conversations.'
            ], 401);
        }

        try {
            $patient = Patient::where('user_id', $user->id)->firstOrFail();

            $conversations = ChatConversation::where('patient_id', $patient->id)
                ->with(['latestMessage', 'assignedTo'])
                ->orderBy('last_message_at', 'desc')
                ->paginate($request->get('per_page', 20));

            // Add unread counts to each conversation
            $conversations->getCollection()->transform(function ($conv) use ($patient) {
                $conv->unread_count = ChatMessage::where('conversation_id', $conv->conversation_id)
                    ->where('sender_type', '!=', 'patient')
                    ->whereNull('read_at')
                    ->count();
                return $conv;
            });

            return response()->json(['success' => true, 'data' => $conversations]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching conversations: ' . $e->getMessage()
            ], 500);
        }
    }

    // Start new conversation
    public function startConversation(Request $request)
    {
        // Require an appointment to start a conversation so chats are tied to scheduled appointments
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to start conversation.'
            ], 401);
        }

        try {

            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required|exists:appointments,id',
                'type' => 'nullable|in:chat,video',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patient = Patient::where('user_id', $user->id)->firstOrFail();
            // Ensure the appointment exists and belongs to this patient
            $appointment = \App\Models\Appointment::find($request->appointment_id);

            if (!$appointment || $appointment->patient_id !== $patient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found or does not belong to the authenticated patient'
                ], 403);
            }

            // Only allow conversations for scheduled/confirmed appointments
            if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversations can only be started for scheduled or confirmed appointments'
                ], 403);
            }

            $type = $request->get('type', 'chat');

            // If a conversation already exists for this appointment and is not closed, return it (prefer latest active conversation)
            $existing = ChatConversation::where('appointment_id', $appointment->id)
                ->where('status', '!=', 'closed')
                ->orderBy('last_message_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            if ($existing) {
                $responseData = [
                    'conversation_id' => $existing->conversation_id,
                    'welcome_message' => 'Welcome back to your conversation.',
                    'is_new' => false
                ];
                return response()->json(array_merge(['success' => true, 'message' => 'Active conversation found for this appointment'], $responseData));
            }

            // Create new conversation tied to the appointment
            $conversation = ChatConversation::create([
                'conversation_id' => ChatConversation::generateConversationId($patient->id),
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'priority' => $request->priority ?? 'medium',
                'status' => 'active'
            ]);

            // Send welcome message tied to the appointment
            $welcomeMessage = "Hello! Your conversation regarding appointment #{$appointment->id} has started. How can I help you today?";


            \App\Models\ChatAssignment::create([
                'conversation_id' => $conversation->conversation_id,
                'assigned_to' => $appointment->doctor_id,
                'assigned_by' => $patient->id,
                'assigned_at' => now(),
            ]);

            ChatMessage::create([
                'conversation_id' => $conversation->conversation_id,
                'sender_type' => 'patient',
                'message_type' => 'text',
                'message' => $welcomeMessage,
                'sender_id' => $patient->id,
                'delivered_at' => now()
            ]);

            // Default channel name (used for video)
            $channelName = 'appointment_' . $appointment->id;

            $responseData = [
                'conversation_id' => $conversation->conversation_id,
                'welcome_message' => $welcomeMessage,
                'is_new' => true
            ];

            // If video requested, generate Agora tokens for patient and doctor and include in response
            if ($type === 'video') {
                $appID = config('services.agora.key');
                $appCertificate = config('services.agora.secret');

                if (!$appID || !$appCertificate) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Video calling is not configured. Please contact administrator.'
                    ], 500);
                }

                $patientUid = $patient->id;
                $doctorUid = $appointment->doctor_id;
                $role = 1; // publisher
                $expireTimeInSeconds = 3600;
                $privilegeExpiredTs = now()->timestamp + $expireTimeInSeconds;

                $patientToken = RtcTokenBuilder::buildTokenWithUid(
                    $appID,
                    $appCertificate,
                    $channelName,
                    $patientUid,
                    $role,
                    $privilegeExpiredTs
                );

                $doctorToken = RtcTokenBuilder::buildTokenWithUid(
                    $appID,
                    $appCertificate,
                    $channelName,
                    $doctorUid,
                    $role,
                    $privilegeExpiredTs
                );

                $responseData['video'] = [
                    'channel_name' => $channelName,
                    'patient_token' => $patientToken,
                    'patient_uid' => $patientUid,
                    'doctor_token' => $doctorToken,
                    'doctor_uid' => $doctorUid,
                    'expires_at' => now()->addSeconds($expireTimeInSeconds)->toDateTimeString()
                ];
            }

            // Send push notifications to both the assigned doctor (if any) and the patient (confirmation)
            $fcm = $this->getFCM();
            if ($fcm) {
                // Notify doctor
                $doctorUser = \App\Models\User::find($appointment->doctor_id);
                if ($doctorUser && $doctorUser->device_token) {
                    $fcm->sendNotification([$doctorUser->device_token], [
                        'title' => $type === 'video' ? 'Incoming video consultation' : 'New chat conversation',
                        'body' => $type === 'video' ? 'Patient started a video consultation. Tap to join.' : 'Your patient started a chat conversation. Tap to respond.',
                        'appointment_id' => $appointment->id,
                        'channel_name' => $channelName,
                        'conversation_id' => $conversation->conversation_id,
                        'type' => $type === 'video' ? 'video_call' : 'chat'
                    ]);

                    // Persist notification for doctor
                    \App\Models\Notification::create([
                        'user_id' => $doctorUser->id,
                        'type' => $type === 'video' ? 'video_call' : 'chat',
                        'title' => $type === 'video' ? 'Video Consultation started' : 'New chat conversation',
                        'meta_data' => json_encode(['appointment_id' => $appointment->id, 'channel_name' => $channelName, 'conversation_id' => $conversation->conversation_id]),
                        'sender_id' => $patient->id,
                    ]);
                }

                // Notify patient (confirmation)
                if (!empty($user->device_token)) {
                    $fcm->sendNotification([$user->device_token], [
                        'title' => 'Conversation started',
                        'body' => $type === 'video' ? 'Video consultation started. Tap to join.' : 'Chat conversation started. You can message the doctor now.',
                        'appointment_id' => $appointment->id,
                        'channel_name' => $channelName,
                        'conversation_id' => $conversation->conversation_id,
                        'type' => $type === 'video' ? 'video_call' : 'chat'
                    ]);

                    // Persist notification for patient
                    \App\Models\Notification::create([
                        'user_id' => $patient->id,
                        'type' => $type === 'video' ? 'video_call' : 'chat',
                        'title' => $type === 'video' ? 'Video Consultation started' : 'Conversation started',
                        'meta_data' => json_encode(['appointment_id' => $appointment->id, 'channel_name' => $channelName, 'conversation_id' => $conversation->conversation_id]),
                        'sender_id' => $patient->id,
                    ]);
                }
            }

            return response()->json(array_merge(['success' => true, 'message' => 'Conversation started'], $responseData), 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    // Send message
    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:attachment|string',
            'message_type' => 'required|in:text,image,file,appointment,prescription',
            'attachments' => 'nullable|array',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to send message.'
            ], 401);
        }

        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'patient',
            'sender_id' => $patient->id,
            'message_type' => $request->message_type,
            'message' => $request->message,
            'attachments' => $request->attachments,
            'metadata' => $request->metadata,
            'delivered_at' => now()
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Broadcast event for real-time update
        broadcast(new \App\Events\NewChatMessage($message));

        // Send push to assigned agents (or fallback to staff/admins) and persist notifications; also notify patient confirmation
        try {
            $conversation = $conversation; // already available
            $assignedUsers = $conversation->assignedTo()->get();

            if ($assignedUsers->isEmpty()) {
                // fallback: pick staff/admin users
                $assignedUsers = \App\Models\User::whereHas('role', function ($q) {
                    $q->whereIn('slug', ['admin', 'staff', 'doctor']);
                })->whereNotNull('device_token')->get();
            }

            $deviceTokens = [];
            foreach ($assignedUsers as $u) {
                if ($u->device_token) {
                    $deviceTokens[] = $u->device_token;
                }

                // Persist Notification per user (if not the message sender)
                if ($u->id !== $message->sender_id) {
                    \App\Models\Notification::create([
                        'user_id' => $u->id,
                        'type' => 'chat_message',
                        'title' => 'New message from patient',
                        'meta_data' => json_encode(['conversation_id' => $conversation->conversation_id, 'message_id' => $message->id]),
                        'sender_id' => $message->sender_id,
                    ]);
                }
            }

            $fcm = $this->getFCM();
            if ($fcm) {
                if (!empty($deviceTokens)) {
                    $fcm->sendNotification($deviceTokens, [
                        'title' => 'New chat message',
                        'body' => substr($message->message ?? 'You have a new message', 0, 120),
                        'conversation_id' => $conversation->conversation_id,
                        'message_id' => $message->id,
                        'type' => 'chat'
                    ]);
                }

                // Send confirmation notification back to the patient (if they have a device token)
                $patientUser = \App\Models\User::find($patient->user_id);
                if ($patientUser && !empty($patientUser->device_token)) {
                    $fcm->sendNotification([$patientUser->device_token], [
                        'title' => 'Message sent',
                        'body' => substr($message->message ?? 'Your message was sent', 0, 120),
                        'conversation_id' => $conversation->conversation_id,
                        'message_id' => $message->id,
                        'type' => 'chat_confirmation'
                    ]);

                    // Persist notification for patient
                    \App\Models\Notification::create([
                        'user_id' => $patient->id,
                        'type' => 'chat_message',
                        'title' => 'Message sent',
                        'meta_data' => json_encode(['conversation_id' => $conversation->conversation_id, 'message_id' => $message->id]),
                        'sender_id' => $message->sender_id,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Chat API push error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data' => $message
        ]);
    }

    // Get a single message
    public function getMessage($conversationId, $messageId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access message.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $message = ChatMessage::where('id', $messageId)
            ->where('conversation_id', $conversation->conversation_id)
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        $message->sender = $message->sender();

        return response()->json(['success' => true, 'data' => $message]);
    }

    // Update (edit) a patient message
    public function updateMessage(Request $request, $conversationId, $messageId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to update message.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $message = ChatMessage::where('id', $messageId)
            ->where('conversation_id', $conversation->conversation_id)
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        // Only allow sender to edit their own patient message
        if ($message->sender_type !== 'patient' || $message->sender_id !== $patient->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized to edit this message'], 403);
        }

        $message->update([
            'message' => $request->message,
            'metadata' => $request->metadata ?? $message->metadata
        ]);

        // Reload sender and broadcast update
        $message->sender = $message->sender();
        try {
            broadcast(new \App\Events\ChatMessageUpdated($message));
        } catch (\Throwable $e) {
            logger()->error('Chat message update broadcast error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Message updated', 'data' => $message]);
    }

    // Delete a patient message
    public function deleteMessage($conversationId, $messageId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to delete message.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $message = ChatMessage::where('id', $messageId)
            ->where('conversation_id', $conversation->conversation_id)
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        if ($message->sender_type !== 'patient' || $message->sender_id !== $patient->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized to delete this message'], 403);
        }

        $messageId = $message->id;
        $conversationId = $conversation->conversation_id;

        $message->delete();

        // Broadcast deletion so clients can remove it
        try {
            broadcast(new \App\Events\ChatMessageDeleted($conversationId, $messageId));
        } catch (\Throwable $e) {
            logger()->error('Chat message delete broadcast error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Message deleted', 'message_id' => $messageId]);
    }

    // Get conversation messages (supports before_id, after_id, per_page, load_all)
    public function getMessages(Request $request, $conversationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access messages.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        $perPage = $request->get('per_page', 50);
        $beforeId = $request->get('before_id');
        $afterId = $request->get('after_id');
        $loadAll = $request->get('load_all', false);

        $query = $conversation->messages();

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        if ($loadAll) {
            $messages = $query->orderBy('created_at', 'asc')->get();
        } elseif ($afterId) {
            // For polling, return messages after the provided id
            $messages = $query->orderBy('created_at', 'asc')->get();
        } else {
            $messages = $query->orderBy('created_at', 'desc')->paginate($perPage);
        }

        // Load sender for each message
        $messages->each(function ($message) {
            $message->sender = $message->sender();
        });

        // Mark messages as read when loading initial messages (not when polling)
        if (!$afterId) {
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_type', '!=', 'patient')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    // Get quick replies
    public function getQuickReplies()
    {
        $quickReplies = ChatQuickReply::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $quickReplies
        ]);
    }



    // Get conversation details
    public function getConversationDetails($conversationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access conversation details.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::with(['patient', 'appointment', 'assignedTo'])
            ->where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        return response()->json(['success' => true, 'conversation' => $conversation]);
    }

    // Mark conversation messages as read
    public function markConversationRead($conversationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to mark conversation as read.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        $updated = ChatMessage::where('conversation_id', $conversation->conversation_id)
            ->where('sender_type', '!=', 'patient')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    // Rate conversation
    public function rateConversation(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to rate conversation.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        $rating = \App\Models\ChatRating::create([
            'conversation_id' => $conversation->conversation_id,
            'patient_id' => $patient->id,
            'rating' => $request->rating,
            'feedback' => $request->feedback
        ]);

        return response()->json(['success' => true, 'message' => 'Rating saved', 'data' => $rating]);
    }

    // Mark single message as read
    public function markMessageRead($conversationId, $messageId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to mark message as read.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        $message = ChatMessage::where('id', $messageId)
            ->where('conversation_id', $conversation->conversation_id)
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        $message->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Message marked as read']);
    }

    // Upload attachment (multipart/form-data) and return temporary attachment metadata
    public function uploadAttachment(Request $request, $conversationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to upload attachment.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240' // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $path = $file->store('chat_attachments', 'public');

        return response()->json(['success' => true, 'file' => ['path' => $path, 'url' => asset('storage/' . $path)]]);
    }

    // Close conversation
    public function closeConversation($conversationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to close conversation.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        $conversation->update(['status' => 'closed']);

        // Send closure message
        ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'system',
            'message_type' => 'text',
            'message' => 'Conversation closed. Thank you for contacting Bhardwaj Hospital.'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation closed successfully'
        ]);
    }

    // Reopen conversation (patient initiated)
    public function reopenConversation($conversationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to reopen conversation.'
            ], 401);
        }
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        if ($conversation->status !== 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is not closed'], 400);
        }

        $conversation->update(['status' => 'active']);

        // Add system message indicating reopen
        ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'system',
            'message_type' => 'text',
            'message' => 'Conversation reopened by patient.'
        ]);

        // Notify assigned users (doctors/staff) so they see it in their dashboard
        try {
            $assignedUsers = $conversation->assignedTo()->get();
            if ($assignedUsers->isEmpty() && $conversation->doctor_id) {
                $assignedUsers = \App\Models\User::where('id', $conversation->doctor_id)->get();
            }

            $deviceTokens = [];
            foreach ($assignedUsers as $u) {
                if (!empty($u->device_token)) $deviceTokens[] = $u->device_token;

                // Persist notification for assigned user
                if ($u->id !== $patient->id) {
                    \App\Models\Notification::create([
                        'user_id' => $u->id,
                        'type' => 'chat',
                        'title' => 'Conversation reopened',
                        'meta_data' => json_encode(['conversation_id' => $conversation->conversation_id]),
                        'sender_id' => $patient->id,
                    ]);
                }
            }

            $fcm = $this->getFCM();
            if ($fcm && !empty($deviceTokens)) {
                $fcm->sendNotification($deviceTokens, [
                    'title' => 'Conversation reopened',
                    'body' => 'A patient reopened a conversation. Tap to view.',
                    'conversation_id' => $conversation->conversation_id,
                    'type' => 'chat'
                ]);
            }
        } catch (\Throwable $e) {
            logger()->error('Chat API reopen notify error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Conversation reopened successfully', 'conversation_id' => $conversation->conversation_id]);
    }
}
