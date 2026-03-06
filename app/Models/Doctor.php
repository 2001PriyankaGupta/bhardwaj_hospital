<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'user_id', 'last_name', 'email', 'phone', 'license_number', 'specialty_id', 'password',
        'qualifications', 'experience', 'bio', 'profile_image', 'status', 'consultation_fee',
        'new_patient_fee', 'old_patient_fee',
        'average_consultation_time', 'working_days', 'shift_start_time', 'shift_end_time', 'is_verified'
    ];

     protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'working_days' => 'array',
        'consultation_fee' => 'decimal:2',
        'new_patient_fee' => 'decimal:2',
        'old_patient_fee' => 'decimal:2',
        'shift_start_time' => 'datetime:H:i',
        'shift_end_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }


    // Relationships
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function likes()
    {
        return $this->hasMany(DoctorLike::class)->where('is_liked', true);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

   public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function todaysQueues()
    {
        return $this->hasMany(Queue::class)->whereDate('check_in_time', today());
    }

    public function waitingQueues()
    {
        return $this->hasMany(Queue::class)
                    ->whereDate('check_in_time', today())
                    ->where('status', 'waiting');
    }

    public function currentQueue()
    {
        return $this->hasOne(Queue::class)
                    ->whereDate('check_in_time', today())
                    ->where('status', 'in_progress')
                    ->latest('call_time');
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function performances()
    {
        return $this->hasMany(DoctorPerformance::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getWeeklyScheduleAttribute()
    {
        return $this->schedules()->where('is_available', true)->get()->groupBy('day_of_week');
    }

    // Methods
    public function isOnLeave($date = null)
    {
        $date = $date ?: now();

        return $this->leaveApplications()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    public function getPerformanceStats($startDate = null, $endDate = null)
    {
        $query = $this->performances();

        if ($startDate && $endDate) {
            $query->whereBetween('performance_date', [$startDate, $endDate]);
        }

        return $query->selectRaw('
            AVG(average_rating) as avg_rating,
            SUM(total_appointments) as total_appts,
            SUM(completed_appointments) as completed_appts,
            SUM(revenue_generated) as total_revenue,
            AVG(patient_satisfaction_score) as avg_satisfaction
        ')->first();
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Appointments relationship
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    protected static function boot()
    {
        parent::boot();
        // When creating a doctor ensure a corresponding user exists and set user_id
        static::creating(function ($doctor) {
            if (!$doctor->user_id) {
                $user = User::firstOrCreate(
                    ['email' => $doctor->email],
                    [
                        'name' => $doctor->first_name . ' ' . $doctor->last_name,
                        'email' => $doctor->email,
                        'password' => $doctor->password ?? Hash::make('password'),
                        'user_type' => 'doctor',
                        'status' => $doctor->status ?? 'active',
                        'phone' => $doctor->phone,
                        'gender' => 'male',
                        'role_id' => Role::where('slug', 'doctor')->first()->id ?? null,
                    ]
                );
                $doctor->user_id = $user->id;
            }
        });

        // After doctor is created, set the back-reference on the user to point to this doctor
        static::created(function ($doctor) {
            if ($doctor->user && Schema::hasColumn('users', 'doctor_id')) {
                $user = $doctor->user;
                if ($user->doctor_id !== $doctor->id) {
                    $user->doctor_id = $doctor->id;
                    $user->save();
                }
            }
        });
    }

}
