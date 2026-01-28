<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyTriage extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'case_number',
        'patient_name',
        'age',
        'gender',
        'symptoms',
        'triage_level',
        'priority_score',
        'assigned_staff',
        'status',
        'notes',
        'arrival_time',
        'treatment_time',
        'created_by'
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
        'treatment_time' => 'datetime',
    ];

    // Constants for triage levels
    const TRIAGE_RED = 'Red';
    const TRIAGE_YELLOW = 'Yellow';
    const TRIAGE_GREEN = 'Green';
    const TRIAGE_BLUE = 'Blue';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    public static function getTriageLevels()
    {
        return [
            self::TRIAGE_RED => 'Red - Immediate',
            self::TRIAGE_YELLOW => 'Yellow - Emergency',
            self::TRIAGE_GREEN => 'Green - Urgent',
            self::TRIAGE_BLUE => 'Blue - Non-urgent',
        ];
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff');
    }

    // Auto-generate case number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->case_number)) {
                $model->case_number = 'EMG-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}