<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'resource_id',
        'patient_id', 'appointment_date','type', 'start_time', 'end_time',
        'status', 'notes', 'cancellation_reason','queue_number'
    ];

    protected $casts = [
        // Serialize appointment_date as a plain date (YYYY-MM-DD) to avoid timezone shifts in JSON
        'appointment_date' => 'date:Y-m-d',
        // store times as strings (DB time type). Formatting done in controller responses.
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    // Check for scheduling conflicts
    public function hasConflicts()
    {
        // Doctor conflict check - proper overlap detection
        $doctorConflict = self::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $this->appointment_date)
            ->where('id', '!=', $this->id)
            ->where(function($query) {
                // Proper overlap: start_time < other.end_time AND end_time > other.start_time
                $query->where('start_time', '<', $this->end_time)
                      ->where('end_time', '>', $this->start_time);
            })
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->exists();

        // Resource conflict check - proper overlap detection
        $resourceConflict = false;
        if ($this->resource_id) {
            $resourceConflict = self::where('resource_id', $this->resource_id)
                ->where('appointment_date', $this->appointment_date)
                ->where('id', '!=', $this->id)
                ->where(function($query) {
                    // Proper overlap: start_time < other.end_time AND end_time > other.start_time
                    $query->where('start_time', '<', $this->end_time)
                          ->where('end_time', '>', $this->start_time);
                })
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->exists();
        }

        return $doctorConflict || $resourceConflict;
    }

    // Get available time slots for a doctor on specific date
    public static function getAvailableSlots($doctorId, $date)
    {
        $doctor = Doctor::find($doctorId);
        return $doctor ? $doctor->getAvailableSlots($date) : [];
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class, 'appointment_id');
    }

    public function conversation()
    {
        return $this->hasOne(ChatConversation::class, 'appointment_id');
    }




    // Add queue relationship
    public function queue()
    {
        return $this->hasOne(QueueManagement::class, 'appointment_id');
    }
     public function generateQueueNumber()
    {
        $doctor = $this->doctor;
        if (!$doctor) return null;

        $doctorCode = strtoupper(
            substr($doctor->first_name, 0, 1) .
            substr($doctor->last_name, 0, 1)
        );

        $dateCode = date('Ymd', strtotime($this->appointment_date));

        $sequence = Appointment::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $this->appointment_date)
            ->count();

        return "A{$doctorCode}{$dateCode}" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function videoCall()
    {
        return $this->hasOne(VideoCall::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'appointment_id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'appointment_id');
    }

    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class, 'appointment_id');
    }

}
