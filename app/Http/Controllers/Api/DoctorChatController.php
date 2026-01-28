<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DoctorChatController extends Controller
{
    // List conversations assigned to this doctor
    public function conversations(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user || $user->user_type !== 'doctor') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $conversations = ChatConversation::with(['patient', 'latestMessage'])
            ->whereHas('assignments', function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->whereNull('unassigned_at');
            })
            ->orderBy('last_message_at', 'desc')
            ->paginate(25);

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    // Doctor sends message to a conversation
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:chat_conversations,conversation_id',
            'message' => 'required_without:attachments|string',
            'message_type' => 'required|in:text,image,file,appointment,prescription',
            'attachments' => 'nullable|array',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::guard('api')->user();
        if (!$user || $user->user_type !== 'doctor') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->firstOrFail();

        // Ensure doctor is assigned to conversation (or allow if unassigned and doctor is the appointment doctor)
        $assigned = $conversation->assignedTo()->where('users.id', $user->id)->exists();
        if (! $assigned) {
            // fallback: if conversation is linked to an appointment check doctor match
            if ($conversation->appointment_id) {
                $appointment = $conversation->appointment;
                if (!($appointment && $appointment->doctor && $appointment->doctor->email === $user->email)) {
                    return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
        }

        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'doctor',
            'sender_id' => $user->id,
            'message_type' => $request->message_type,
            'message' => $request->message,
            'attachments' => $request->attachments,
            'metadata' => $request->metadata,
            'delivered_at' => now()
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Broadcast event for real-time update
        try {
            broadcast(new \App\Events\NewChatMessage($message));
        } catch (\Throwable $e) {
            logger()->error('Broadcast error (doctor send): ' . $e->getMessage());
        }

        // Notify patient via push + persist Notification
        try {
            $patient = $conversation->patient;
            if ($patient && $patient->email) {
                $patientUser = User::where('email', $patient->email)->first();
                if ($patientUser) {
                    // Persist Notification
                    \App\Models\Notification::create([
                        'user_id' => $patientUser->id,
                        'type' => 'chat_message',
                        'title' => 'New message from your doctor',
                        'meta_data' => json_encode(['conversation_id' => $conversation->conversation_id, 'message_id' => $message->id]),
                        'sender_id' => $user->id,
                    ]);

                    if ($patientUser->device_token) {
                        $projectId = config('services.firebase.project_id');
                        $credentialsPath = public_path(config('services.firebase.credentials_path'));
                        $fcm = new \App\Services\FirebaseService($projectId, $credentialsPath);

                        $fcm->sendNotification([$patientUser->device_token], [
                            'title' => 'Message from your doctor',
                            'body' => substr($message->message ?? 'You have a new message', 0, 140),
                            'conversation_id' => $conversation->conversation_id,
                            'message_id' => $message->id,
                            'type' => 'chat'
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Doctor chat push error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Message sent', 'data' => $message]);
    }

    // Get conversation messages for doctor (mirror of patient API)
    public function getMessages($conversationId)
    {
        $user = Auth::guard('api')->user();
        if (!$user || $user->user_type !== 'doctor') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)->firstOrFail();

        // Ensure doctor has access
        $assigned = $conversation->assignedTo()->where('users.id', $user->id)->exists();
        if (! $assigned) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $messages = $conversation->messages()->orderBy('created_at', 'desc')->paginate(50);

        // Load senders
        $messages->getCollection()->transform(function ($message) {
            $message->sender = $message->sender();
            return $message;
        });

        // Mark messages as read for doctor
        ChatMessage::where('conversation_id', $conversationId)
            ->where('sender_type', '!=', 'doctor')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'conversation' => $conversation, 'messages' => $messages]);
    }
}
