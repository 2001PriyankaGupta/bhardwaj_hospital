<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueManagement extends Model
{
    use HasFactory;
    protected $table = 'queue_managements';

    protected $fillable = [
        'queue_number',
        'patient_id',
        'doctor_id',
        'queue_type',
        'status',
        'estimated_wait_time',
        'priority_score',
        'reason_for_visit',
        'check_in_time',
        'called_at',
        'consultation_start_time',
        'consultation_end_time',
        'current_room',
        'is_priority',
        'position',
        'vital_signs',
        'notes',
        'appointment_id'
    ];

    protected $casts = [
        'vital_signs' => 'array',
        'check_in_time' => 'datetime',
        'called_at' => 'datetime',
        'consultation_start_time' => 'datetime',
        'consultation_end_time' => 'datetime',
        'is_priority' => 'boolean',
    ];


    // Add appointment relationship
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Scopes
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'in_progress']);
    }

    public function markAsCalled()
    {
        $this->update([
            'status' => 'in_progress',
            'called_at' => now()
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'consultation_end_time' => now()
        ]);
    }

public function getEstimatedWaitTimeAttribute($value)
{
    return $value ? round($value) : 0;
}

// Update the calculatePriority method
public function calculatePriority()
{
    $score = 0;
    
    // Queue type priority
    if ($this->queue_type === 'emergency') {
        $score += 10;
    } elseif ($this->queue_type === 'follow_up') {
        $score += 2;
    }
    
    // Priority flag
    if ($this->is_priority) {
        $score += 5;
    }
    
    // Age-based priority
    if ($this->patient && $this->patient->date_of_birth) {
        $age = now()->diffInYears($this->patient->date_of_birth);
        if ($age < 5) {
            $score += 3; // Children under 5
        } elseif ($age > 65) {
            $score += 3; // Elderly above 65
        } elseif ($age < 18) {
            $score += 1; // Teenagers
        }
    }
    
    // Time waiting priority (increases every 30 minutes)
    if ($this->check_in_time) {
        $waitingMinutes = now()->diffInMinutes($this->check_in_time);
        if ($waitingMinutes > 30) {
            $score += floor($waitingMinutes / 30);
        }
    }
    
    return min($score, 100); // Cap at 100
}


}