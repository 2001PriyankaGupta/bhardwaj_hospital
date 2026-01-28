<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;

class ChatController extends Controller
{
    public function index()
    {
        $doctorUser = Auth::user();
        // Use the authenticated user's ID (assigned_to stores a users.id) — not the doctor model id
        $doctorUserId = $doctorUser->id;

        $doctor = Doctor::where('user_id', $doctorUserId)->first();

        if (!$doctor) {
            $conversations = collect();
        } else {
            // Conversations for this doctor
            $conversations = ChatConversation::with(['patient', 'latestMessage'])
                ->where('doctor_id', $doctor->id)
                ->orderBy('updated_at', 'desc')
                ->limit(50)
                ->get();
        }

        // Remove duplicate conversations for the same patient (keep the most recent).
        // Use patient_id when present, otherwise fall back to conversation_id as dedupe key.
        $conversations = $conversations->unique(function($item) {
            return $item->patient_id ?? $item->conversation_id;
        })->values();

        return view('doctor.chat.index', compact('conversations', 'doctor'));
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
        ]);

        $doctorUser = Auth::user();
        $doctor = Doctor::where('user_id', $doctorUser->id)->first();

        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        $appointment = \App\Models\Appointment::find($request->appointment_id);

        if (!$appointment || $appointment->doctor_id !== $doctor->id) {
            return response()->json(['success' => false, 'message' => 'Appointment not found or does not belong to you'], 403);
        }

        // Check if an active conversation already exists for this patient (any appointment)
        $existing = ChatConversation::where('patient_id', $appointment->patient_id)
            ->where('status', '!=', 'closed')
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation already exists',
                'conversation_id' => $existing->conversation_id
            ]);
        }

        // Create new conversation
        $conversation = ChatConversation::create([
            'conversation_id' => ChatConversation::generateConversationId($appointment->patient_id),
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'status' => 'active',
            'priority' => 'medium',
            'last_message_at' => now(),
        ]);

        // Assign to the doctor
        \App\Models\ChatAssignment::create([
            'conversation_id' => $conversation->conversation_id,
            'assigned_to' => $doctorUser->id,
            'assigned_by' => $doctorUser->id,
            'assigned_at' => now(),
        ]);

        // Send initial message
        \App\Models\ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'doctor',
            'sender_id' => $doctorUser->id,
            'message_type' => 'text',
            'message' => "Hello! I've started a conversation regarding your appointment on " . $appointment->appointment_date . " at " . $appointment->start_time . ". How can I help you?",
            'delivered_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation started successfully',
            'conversation_id' => $conversation->conversation_id
        ]);
    }

    public function getAppointmentsForChat()
    {
        $doctorUser = Auth::user();
        $doctor = Doctor::where('user_id', $doctorUser->id)->first();

        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        $appointments = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments->map(function($appt) {
                return [
                    'id' => $appt->id,
                    'date' => $appt->appointment_date,
                    'time' => $appt->start_time,
                    'patient_name' => ($appt->patient->first_name ?? '') . ' ' . ($appt->patient->last_name ?? ''),
                    'status' => $appt->status
                ];
            })
        ]);
    }

    public function getConversation($conversationId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::with(['patient', 'appointment'])
            ->where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
            ->firstOrFail();

        return response()->json(['success' => true, 'conversation' => $conversation]);
    }

    public function getMessages(Request $request, $conversationId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;
        $doctor = Doctor::where('user_id', $doctorUserId)->first();

        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        // Verify access to conversation - assigned, active, or doctor's own appointment conversations
        $conversation = ChatConversation::where('conversation_id', $conversationId)->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        // Check if doctor has access to this conversation
        $hasAccess = $conversation->status === 'active' ||
                    $conversation->doctor_id === $doctor->id ||
                    $conversation->assignments()->where('assigned_to', $doctorUserId)->exists();

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Access denied to this conversation'], 403);
        }

        $perPage = $request->get('per_page', 50); // Load more messages by default
        $beforeId = $request->get('before_id');
        $afterId = $request->get('after_id');
        $loadAll = $request->get('load_all', false); // New parameter to load all messages

        $query = ChatMessage::where('conversation_id', $conversationId);

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        // If load_all is true, get all messages, otherwise paginate
        if ($loadAll) {
            $messages = $query->orderBy('created_at', 'asc')->get();
            $hasMore = false;
        } elseif ($afterId) {
            // For polling, get recent messages
            $messages = $query->orderBy('created_at', 'asc')->get();
            $hasMore = false;
        } else {
            $messages = $query->orderBy('created_at', 'asc')->limit($perPage)->get();
            $hasMore = $messages->count() === $perPage;
        }

        // Load sender information for each message
        $messages->transform(function ($message) {
            $sender = $message->sender();

            $message->sender_name = $sender->name ?? 'Unknown User';
            $message->sender_type_display = ucfirst($sender->type ?? $message->sender_type);

            return $message;
        });

        // Mark messages as read if they're not from the current doctor
        if (!$afterId) { // Only mark as read when loading initial messages, not when polling
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_type', '!=', 'doctor')
                ->where('sender_id', '!=', $doctorUserId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages->toArray(),
            'has_more' => $hasMore,
            'conversation_status' => $conversation->status
        ]);
    }

    public function sendMessage(Request $request)
    {
        // Validate input first
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'conversation_id' => 'required|exists:chat_conversations,conversation_id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            logger()->warning('Doctor sendMessage validation failed', ['errors' => $validator->errors(), 'payload' => $request->all(), 'user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        // Check that at least message or file is provided
        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Either message or file is required'], 422);
        }

        // Diagnostic log: capture incoming request and authenticated user
        logger()->info('Doctor sendMessage request', ['user_id' => Auth::id(), 'payload' => $request->all()]);

        $user = Auth::user();
        $doctorUserId = $user->id;

        // Find doctor model if needed (not used for assignment checks)
        $doctorModel = Doctor::where('user_id', $doctorUserId)->first();

        // First, check if conversation exists and is not closed
        $conversation = \App\Models\ChatConversation::where('conversation_id', $request->conversation_id)->first();
        if (!$conversation) {
            logger()->warning('Doctor sendMessage: conversation not found', ['conversation_id' => $request->conversation_id, 'user_id' => $doctorUserId]);
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }
        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        // Check if the user is assigned to this conversation (assigned_to stores users.id)
        $assignmentQuery = \App\Models\ChatAssignment::where('conversation_id', $request->conversation_id)
            ->where('assigned_to', $doctorUserId)
            ->whereNull('unassigned_at');

        $isAssigned = $assignmentQuery->exists();

        if (!$isAssigned) {
            // Attempt to auto-assign the conversation to this doctor if the conversation is active
            if ($conversation->status !== 'active') {
                // Auto-activate the conversation so doctors can reply even if not previously active
                logger()->info('Doctor sendMessage: auto-activating conversation before assigning', ['conversation_id' => $request->conversation_id, 'old_status' => $conversation->status, 'user_id' => $doctorUserId]);
                $conversation->update(['status' => 'active']);
            }

            // Create a new assignment for this doctor (store user id) and proceed
            $assignment = \App\Models\ChatAssignment::create([
                'conversation_id' => $request->conversation_id,
                'assigned_to' => $doctorUserId,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);
            logger()->info('Doctor sendMessage: auto-assigned conversation to doctor', ['assignment_id' => $assignment->id ?? null, 'conversation_id' => $request->conversation_id, 'user_id' => $doctorUserId, 'assignment' => $assignment ? $assignment->toArray() : null]);
        }

        try {
            $fileData = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('chat_attachments', $filename, 'public');
                $fileData = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }

            logger()->info('About to create message', ['conversation_id' => $request->conversation_id, 'sender_type' => 'doctor', 'sender_id' => $user->id, 'message' => $request->message, 'file' => $fileData ? $fileData['name'] : null]);

            $message = ChatMessage::create([
                'conversation_id' => $request->conversation_id,
                'sender_type' => 'doctor',
                'sender_id' => $doctorModel->id,  // Use doctor id, not user id
                'message_type' => $fileData ? 'file' : 'text',
                'message' => $request->message,
                'attachments' => $fileData ? [$fileData] : null,
                'delivered_at' => now()
            ]);

            logger()->info('Message created successfully', ['message_id' => $message->id, 'message' => $message->toArray()]);

            // Load sender data for response (don't set as attribute to avoid update issues)
            $senderData = $message->sender();

            // Update conversation
            $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();
            if ($conversation) {
                $conversation->update([
                    'last_message_at' => now(),
                    'status' => 'active'
                ]);
                logger()->info('Conversation updated', ['conversation_id' => $request->conversation_id]);
            }

            // Mark message as delivered immediately for the sender (only update delivered_at)
            $message->update(['delivered_at' => now()]);

            // Diagnostic log: message created
            logger()->info('Doctor sendMessage created', ['message_id' => $message->id, 'conversation_id' => $request->conversation_id, 'sender_id' => $doctorModel->id, 'message_preview' => substr($request->message, 0, 120)]);

            // Broadcast for real-time and let admin controller handle push if configured
            try {
                broadcast(new \App\Events\NewChatMessage($message));
            } catch (\Throwable $e) {
                logger()->error('Broadcast failed for doctor sendMessage: ' . $e->getMessage());
            }

            // Try sending push to patient (mirror admin behavior)
            $notificationSent = null;
            try {
                $conversation = \App\Models\ChatConversation::where('conversation_id', $request->conversation_id)->first();
                $patient = $conversation->patient ?? null;
                if ($patient && $patient->email) {
                    $patientUser = \App\Models\User::where('email', $patient->email)->first();
                    if ($patientUser && $patientUser->device_token) {
                        $pushResult = \App\Helpers\FirebaseNotification::send(
                            $patientUser->device_token,
                            'New message from clinic',
                            substr($message->message ?? 'You have a new message', 0, 120),
                            [
                                'conversation_id' => $conversation->conversation_id,
                                'message_id' => $message->id,
                                'type' => 'chat'
                            ]
                        );

                        $notificationSent = $pushResult === true;

                        \App\Models\Notification::create([
                            'user_id' => $patientUser->id,
                            'type' => 'chat_message',
                            'title' => 'New message from clinic',
                            'meta_data' => json_encode(['conversation_id' => $conversation->conversation_id, 'message_id' => $message->id]),
                            'sender_id' => Auth::id(),
                        ]);

                        if (!$notificationSent) {
                            logger()->warning('Doctor->patient push not sent (credentials missing or FCM error).', ['patient_id' => $patientUser->id, 'conversation_id' => $conversation->conversation_id, 'message_id' => $message->id, 'result' => $pushResult]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                logger()->error('Chat->patient push error: ' . $e->getMessage());
            }

            // Return the created message in `data` for frontend to append immediately
            return response()->json([
                'success' => true,
                'data' => array_merge($message->toArray(), ['sender' => $senderData]),
                'notification_sent' => $notificationSent,
                'message' => 'Message sent successfully'
            ]);
        } catch (\Throwable $e) {
            // Log request and exception for debugging
            logger()->error('Doctor sendMessage failed', [
                'user_id' => $user->id ?? null,
                'conversation_id' => $request->conversation_id,
                'message' => $request->message,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Server error when sending message'], 500);
        }
    }

    public function getMessage($conversationId, $messageId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
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

        $message->sender = $message->sender();

        return response()->json(['success' => true, 'data' => $message]);
    }

    public function updateMessage(Request $request, $conversationId, $messageId)
    {
        $request->validate([
            'message' => 'required|string',
            'metadata' => 'nullable|array'
        ]);

        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $doctorModel = Doctor::where('user_id', $doctorUserId)->first();
        if (!$doctorModel) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
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

        // Only allow doctor to edit their own messages
        if ($message->sender_type !== 'doctor' || $message->sender_id !== $doctorModel->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized to edit this message'], 403);
        }

        $message->update([
            'message' => $request->message,
            'metadata' => $request->metadata ?? $message->metadata
        ]);

        $message->sender = $message->sender();

        try {
            broadcast(new \App\Events\ChatMessageUpdated($message));
        } catch (\Throwable $e) {
            logger()->error('Doctor chat message update broadcast error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Message updated', 'data' => $message]);
    }

    public function deleteMessage($conversationId, $messageId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $doctorModel = Doctor::where('user_id', $doctorUserId)->first();
        if (!$doctorModel) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
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

        if ($message->sender_type !== 'doctor' || $message->sender_id !== $doctorModel->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized to delete this message'], 403);
        }

        $messageId = $message->id;
        $conversationId = $conversation->conversation_id;

        $message->delete();

        try {
            broadcast(new \App\Events\ChatMessageDeleted($conversationId, $messageId));
        } catch (\Throwable $e) {
            logger()->error('Doctor chat message delete broadcast error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Message deleted', 'message_id' => $messageId]);
    }

    public function markConversationRead($conversationId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        $updated = ChatMessage::where('conversation_id', $conversation->conversation_id)
            ->where('sender_type', '!=', 'doctor')
            ->where('sender_id', '!=', $doctorUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    public function markMessageRead($conversationId, $messageId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
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

    public function uploadAttachment(Request $request, $conversationId)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // 10MB
        ]);

        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $file = $request->file('file');
        $path = $file->store('chat_attachments', 'public');

        return response()->json(['success' => true, 'file' => ['path' => $path, 'url' => asset('storage/' . $path)]]);
    }

    public function closeConversation($conversationId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
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
            'message' => 'Conversation closed by doctor.'
        ]);

        return response()->json(['success' => true, 'message' => 'Conversation closed successfully']);
    }

    public function reopenConversation($conversationId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;
        $doctor = Doctor::where('user_id', $doctorUserId)->first();

        if (!$doctor) {
            return response()->json(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId, $doctor) {
                $q->where('doctor_id', $doctor->id)
                  ->orWhereHas('assignments', function ($subQ) use ($doctorUserId) {
                      $subQ->where('assigned_to', $doctorUserId);
                  });
            })
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found or you do not have permission to reopen it'], 404);
        }

        // Update status to active
        $conversation->update(['status' => 'active']);

        // Add system message
        ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'system',
            'message_type' => 'text',
            'message' => 'Conversation reopened by doctor.'
        ]);

        return response()->json(['success' => true, 'message' => 'Conversation reopened successfully', 'conversation_id' => $conversation->conversation_id]);
    }

    public function getConversationDetails($conversationId)
    {
        $doctorUser = Auth::user();
        $doctorUserId = $doctorUser->id;

        $conversation = ChatConversation::with(['patient', 'appointment', 'assignedTo'])
            ->where('conversation_id', $conversationId)
            ->where(function($q) use ($doctorUserId) {
                $q->whereHas('assignments', function ($subQ) use ($doctorUserId) {
                    $subQ->where('assigned_to', $doctorUserId);
                })->orWhere('status', 'active');
            })
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        return response()->json(['success' => true, 'conversation' => $conversation]);
    }
}
