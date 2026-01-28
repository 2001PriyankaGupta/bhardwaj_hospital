<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'channel_name',
        'token',
        'status',
        'started_at',
        'ended_at',
        'duration',
        'call_data'
    ];

    protected $casts = [
        'call_data' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ongoing');
    }
}