<?php

use Illuminate\Broadcasting\Broadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Broadcast as BroadcastFacade;

/**
 * Here you may register all of the event broadcasting channels that your
 * application supports. The given channel authorization callbacks are
 * used to check if an authenticated user can listen to the channel.
 */

Broadcast::channel('doctor.{id}', function ($user, $id) {
    // Allow the doctor themselves (matching doctor_id) or an admin/staff
    if (!$user) return false;
    if (in_array($user->user_type, ['admin', 'staff'])) return true;
    return ($user->user_type === 'doctor' && $user->doctor_id == $id);
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    if (!$user) return false;
    $conv = \App\Models\ChatConversation::where('conversation_id', $conversationId)->first();
    if (!$conv) return false;

    // Doctor assigned to conversation
    if ($user->user_type === 'doctor' && $user->doctor_id == $conv->user_id) return true;

    // Patient (match by email)
    if ($user->user_type === 'patient') {
        $patient = \App\Models\Patient::find($conv->patient_id);
        return $patient && $patient->email === $user->email;
    }

    // Allow admin/staff for support
    return in_array($user->user_type, ['admin', 'staff']);
});
