<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
     use HasFactory;
    protected $fillable = [
        'patient_id', 'record_type', 'description', 'notes', 
        'attachment', 'record_date', 'created_by'
    ];

    protected $casts = [
        'record_date' => 'date'
    ];

    // Patient relationship
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Doctor relationship
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Department relationship
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Appointment relationship
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
