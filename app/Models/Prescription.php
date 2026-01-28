<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'medical_record_id',
        'appointment_id',
        'patient_id',
        'doctor_id',
        'medication_details',
        'instructions',
        'follow_up_advice',
        'prescription_date',
        'valid_until',
        'is_active'
    ];

    protected $casts = [
        'medication_details' => 'array',
        'prescription_date' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function medicalRecord()
    {
        return $this->belongsTo(PatientMedicalRecord::class, 'medical_record_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}