<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'profile_picture',
        'is_admin',
        'user_type',
        'doctor_id',
        'staff_id',
        'status',
        'locality',
        'department_id',
        'age',
        'address',
        'emergency_contact_number',
        'alternate_contact_number',
        'basic_medical_history',
        'otp',
        'otp_expires_at',
        'is_verified',
        'role_id',
        'device_token',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    protected $attributes = [
        'is_admin' => 0,
        'user_type' => 'patient',
        'status' => 'active',
    ];

    // Gender constants for easy access
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHER = 'other';


    public static function getGenderOptions()
    {
        return [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER => 'Other',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('is_admin', false);
    }



    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }

        return $role->intersect($this->roles)->isNotEmpty();
    }



    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }


    public function isDoctor()
    {
        return $this->user_type === 'doctor';
    }



     public function availability()
    {
        return $this->hasOne(DoctorAvailability::class, 'doctor_id');
    }

    public function queues()
    {
        return $this->hasMany(PatientQueue::class, 'doctor_id');
    }


    public function patientQueues()
    {
        return $this->hasMany(PatientQueue::class, 'patient_id');
    }


    public function appointmentsAsDoctor()
    {
        return $this->hasManyThrough(Appointment::class, Doctor::class);
    }

    public function appointmentsAsPatient()
    {
        return $this->hasManyThrough(Appointment::class, Patient::class);
    }

    public function primaryDoctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function primaryStaff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permissionSlug)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionSlug);
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin' || $this->role?->slug === 'admin';
    }

    public function isStaff()
    {
        return $this->user_type === 'staff' || $this->role?->slug === 'staff';
    }



}
