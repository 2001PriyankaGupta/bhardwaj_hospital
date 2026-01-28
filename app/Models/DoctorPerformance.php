<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'performance_date', 'total_appointments', 'completed_appointments',
        'cancelled_appointments', 'no_show_appointments', 'average_rating', 'total_reviews',
        'revenue_generated', 'patient_satisfaction_score', 'remarks'
    ];

    protected $casts = [
        'performance_date' => 'date',
        'average_rating' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getSuccessRateAttribute()
    {
        if ($this->total_appointments == 0) return 0;
        return ($this->completed_appointments / $this->total_appointments) * 100;
    }

    public function getCancellationRateAttribute()
    {
        if ($this->total_appointments == 0) return 0;
        return ($this->cancelled_appointments / $this->total_appointments) * 100;
    }
}