<?php

// app/Models/ChatConversation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $fillable = [
        'conversation_id',
        'patient_id',
        'doctor_id',
        'department_id',
        'appointment_id',
        'status',
        'priority',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    /**
     * Allowed priorities for conversations. Keep in sync with DB enum.
     */
    protected static $allowedPriorities = ['low', 'medium', 'high', 'emergency'];

    /**
     * Normalize priority values before saving to DB to avoid enum insertion errors.
     */
    public function setPriorityAttribute($value)
    {
        $this->attributes['priority'] = in_array($value, self::$allowedPriorities) ? $value : 'medium';
    }

    // Generate unique conversation ID
    public static function generateConversationId($patientId = null)
    {
        return 'CHAT-' . ($patientId ?? 'TEMP') . '-' . time() . '-' . rand(1000, 9999);
    }

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id', 'conversation_id')
                    ->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id', 'conversation_id')
                    ->orderBy('created_at', 'desc');
    }

    public function assignments()
    {
        return $this->hasMany(ChatAssignment::class, 'conversation_id', 'conversation_id')
                    ->whereNull('unassigned_at');
    }

    public function assignedTo()
    {
        return $this->belongsToMany(User::class, 'chat_assignments', 'conversation_id', 'assigned_to')
                    ->whereNull('chat_assignments.unassigned_at');
    }
}
