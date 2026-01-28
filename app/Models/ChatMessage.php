<?php

// app/Models/ChatMessage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message_type',
        'message',
        'attachments',
        'metadata',
        'read_at',
        'delivered_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id', 'conversation_id');
    }

    public function sender()
    {
        if ($this->sender_type === 'patient') {
            $patient = Patient::find($this->sender_id);
            if ($patient) {
                return (object) [
                    'id' => $patient->id,
                    'name' => trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')),
                    'type' => 'patient'
                ];
            }
        } elseif (in_array($this->sender_type, ['admin', 'doctor', 'staff'])) {
            $doctor = Doctor::where('id', $this->sender_id)->first();
            if ($doctor) {
                return (object) [
                    'id' => $doctor->id,
                    'name' => trim(($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? '')),
                    'type' => $this->sender_type
                ];
            }
            $staff = Staff::where('id', $this->sender_id)->first();
            if ($staff) {
                return (object) [
                    'id' => $staff->id,
                    'name' => $staff->name ?? 'Unknown Staff',
                    'type' => $this->sender_type
                ];
            }
        }

        // Fallback
        return (object) [
            'id' => $this->sender_id,
            'name' => 'Unknown User',
            'type' => $this->sender_type
        ];
    }
}
