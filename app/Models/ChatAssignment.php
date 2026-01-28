<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAssignment extends Model
{
    protected $table = 'chat_assignments';

    // We use custom timestamp columns (assigned_at / unassigned_at)
    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'unassigned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id', 'conversation_id');
    }
}
