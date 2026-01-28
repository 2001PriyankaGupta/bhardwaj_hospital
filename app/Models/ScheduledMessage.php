<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'recipients',
        'variables',
        'scheduled_at',
        'status',
        'sent_at',
        'error_message'
    ];

    protected $casts = [
        'recipients' => 'array',
        'variables' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    // Scope for pending messages
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for upcoming messages (include messages scheduled exactly at now)
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now());
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null
        ]);
    }

    // Mark as failed
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }
}
