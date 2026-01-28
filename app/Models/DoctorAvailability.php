<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorAvailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'status',
        'current_patient_id',
        'room_number',
        'max_patients_per_day',
        'patients_seen_today',
        'break_start',
        'break_end',
        'is_accepting_patients',
        'estimated_wait_time',
        'schedule'
    ];

    protected $casts = [
        'schedule' => 'array',
        'is_accepting_patients' => 'boolean',
        'break_start' => 'datetime',
        'break_end' => 'datetime'
    ];

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_BUSY = 'busy';
    const STATUS_BREAK = 'break';
    const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * Get the doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get current patient
     */
    public function currentPatient()
    {
        return $this->belongsTo(User::class, 'current_patient_id');
    }

    /**
     * Check if doctor is available
     */
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE 
            && $this->is_accepting_patients
            && $this->patients_seen_today < $this->max_patients_per_day;
    }

    /**
     * Check if doctor is on break
     */
    public function isOnBreak()
    {
        if ($this->status === self::STATUS_BREAK) {
            return true;
        }

        if ($this->break_start && $this->break_end) {
            $now = now();
            return $now->between(
                today()->setTimeFromTimeString($this->break_start),
                today()->setTimeFromTimeString($this->break_end)
            );
        }

        return false;
    }

    /**
     * Reset daily counters
     */
    public function resetDailyCounters()
    {
        $this->update([
            'patients_seen_today' => 0
        ]);
    }

    /**
     * Update status
     */
    public function updateStatus($status, $patientId = null)
    {
        $this->update([
            'status' => $status,
            'current_patient_id' => $patientId
        ]);
    }
}