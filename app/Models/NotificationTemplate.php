<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'subject',
        'content',
        'variables',
        'status'
    ];

    protected $casts = [
        'variables' => 'array',
        'status' => 'boolean'
    ];

    public function scheduledMessages()
    {
        return $this->hasMany(ScheduledMessage::class);
    }

    // Scope for active templates
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}