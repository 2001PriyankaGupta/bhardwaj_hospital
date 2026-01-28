<?php

// app/Http/Controllers/admin/ChatController.php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Dashboard for admin chat
    public function dashboard()
    {
        $stats = [
            'active_chats' => ChatConversation::where('status', 'active')->count(),
            'pending_chats' => ChatConversation::where('status', 'pending')->count(),
            'assigned_to_me' => ChatConversation::whereHas('assignments', function($q) {
                $q->where('assigned_to', Auth::id())->whereNull('unassigned_at');
            })->where('status', 'active')->count(),
            'today_chats' => ChatConversation::whereDate('created_at', today())->count()
        ];

        $conversations = ChatConversation::with(['patient', 'latestMessage', 'assignedTo'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        $availableAgents = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['admin', 'doctor', 'support_staff']);
            })
            ->where('is_active', true)
            ->get(['id', 'name', 'email', 'profile_picture']);

        return view('admin.chat.dashboard', compact('stats', 'conversations', 'availableAgents'));
    }

    // Get conversation details
    public function getConversation($conversationId)
    {
        $conversation = ChatConversation::with(['patient', 'assignments.assignedTo'])
            ->where('conversation_id', $conversationId)
            ->firstOrFail();

        // Load senders for messages
        $conversation->messages->each(function ($message) {
            $message->sender = $message->sender();
        });

        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }

    // Admin send message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,conversation_id',
            'message' => 'required|string',
            'message_type' => 'in:text,image,file'
        ]);

        $user = Auth::user();

        // Check if conversation is closed
        $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();
        if ($conversation->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Conversation is closed'], 403);
        }

        $message = ChatMessage::create([
            'conversation_id' => $request->conversation_id,
            'sender_type' => ($user->user_type === 'doctor') ? 'doctor' : 'admin',
            'sender_id' => $user->id,
            'message_type' => $request->message_type ?? 'text',
            'message' => $request->message,
            'delivered_at' => now()
        ]);

        // Update conversation
        $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();
        $conversation->update([
            'last_message_at' => now(),
            'status' => 'active'
        ]);

        // Broadcast event
        broadcast(new \App\Events\NewChatMessage($message));

        // Send push to patient if exists
        try {
            $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();
            $patient = $conversation->patient;
            if ($patient && $patient->email) {
                $patientUser = \App\Models\User::where('email', $patient->email)->first();
                if ($patientUser && $patientUser->device_token) {
                    \App\Helpers\FirebaseNotification::send(
                        $patientUser->device_token,
                        'New message from clinic',
                        substr($message->message ?? 'You have a new message', 0, 120),
                        [
                            'conversation_id' => $conversation->conversation_id,
                            'message_id' => $message->id,
                            'type' => 'chat'
                        ]
                    );

                    \App\Models\Notification::create([
                        'user_id' => $patientUser->id,
                        'type' => 'chat_message',
                        'title' => 'New message from clinic',
                        'meta_data' => json_encode(['conversation_id' => $conversation->conversation_id, 'message_id' => $message->id]),
                        'sender_id' => Auth::id(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Chat->patient push error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // Assign conversation to agent
    public function assignConversation(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,conversation_id',
            'assigned_to' => 'required|exists:users,id'
        ]);

        $conversation = ChatConversation::where('conversation_id', $request->conversation_id)->first();

        // Unassign previous assignments
        \App\Models\ChatAssignment::where('conversation_id', $request->conversation_id)
            ->whereNull('unassigned_at')
            ->update(['unassigned_at' => now()]);

        // Create new assignment
        $assignment = \App\Models\ChatAssignment::create([
            'conversation_id' => $request->conversation_id,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => Auth::id()
        ]);

        // Send assignment notification
        ChatMessage::create([
            'conversation_id' => $conversation->conversation_id,
            'sender_type' => 'system',
            'message_type' => 'text',
            'message' => 'Conversation assigned to ' . User::find($request->assigned_to)->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation assigned successfully'
        ]);
    }

    // Update conversation status
    public function updateStatus(Request $request, $conversationId)
    {
        $request->validate([
            'status' => 'required|in:pending,active,closed,resolved'
        ]);

        $conversation = ChatConversation::where('conversation_id', $conversationId)->firstOrFail();
        $conversation->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    // Get chat statistics for dashboard
    public function getChatStats()
    {
        $stats = [
            'total_conversations' => ChatConversation::count(),
            'active_conversations' => ChatConversation::where('status', 'active')->count(),
            'resolved_today' => ChatConversation::where('status', 'resolved')
                ->whereDate('updated_at', today())
                ->count(),
            'average_response_time' => $this->calculateAverageResponseTime()
        ];

        // Department-wise chat distribution
        $departmentStats = ChatConversation::with('department')
            ->selectRaw('department_id, COUNT(*) as count')
            ->whereNotNull('department_id')
            ->groupBy('department_id')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'department_stats' => $departmentStats
        ]);
    }

    private function calculateAverageResponseTime()
    {
        // Logic to calculate average response time
        return '2m 15s'; // Example
    }
}
