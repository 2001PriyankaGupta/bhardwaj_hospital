<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;


class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'medical_history',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(PatientMedicalRecord::class);
    }

    public function communicationLogs()
    {
        return $this->hasMany(CommunicationLog::class);
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }


    // Patient analytics - get appointment statistics
    public function getAppointmentStats()
    {
        return [
            'total_appointments' => $this->appointments()->count(),
            'completed_appointments' => $this->appointments()->where('status', 'completed')->count(),
            'upcoming_appointments' => $this->appointments()->where('status', 'scheduled')->count(),
        ];
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    // Generate patient ID
    public static function generatePatientId()
    {
        do {
            $latestPatient = Patient::latest('id')->first();

            if (!$latestPatient) {
                $newPatientId = 'PID000001';
            } else {
                $lastId = $latestPatient->patient_id;
                $number = intval(substr($lastId, 3)) + 1;
                $newPatientId = 'PID' . str_pad($number, 6, '0', STR_PAD_LEFT);
            }

            // Check if this ID already exists
            $existingPatient = Patient::where('patient_id', $newPatientId)->first();
        } while ($existingPatient); // Continue generating until unique ID found

        return $newPatientId;
    }


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($patient) {
            if (!$patient->user_id) {
                $user = User::firstOrCreate(
                    ['email' => $patient->email],
                    [
                        'name' => $patient->first_name . ' ' . $patient->last_name,
                        'email' => $patient->email,
                        'password' => Hash::make('password'),
                        'user_type' => 'patient',
                        'status' => $patient->is_active ? 'active' : 'inactive',
                        'phone' => $patient->phone,
                        'gender' => $patient->gender,
                        'age' => \Carbon\Carbon::parse($patient->date_of_birth)->age,
                        'address' => $patient->address,
                        'basic_medical_history' => $patient->medical_history,
                        'emergency_contact_number' => $patient->emergency_contact_number ?? null,
                        'alternate_contact_number' => $patient->alternate_contact_number ?? null,
                        'role_id' => Role::where('slug', 'patient')->first()->id ?? null,
                    ]
                );
                $patient->user_id = $user->id;
            }
        });

        // After patient is created, ensure the user points back to this patient
        static::created(function ($patient) {
            if ($patient->user) {
                $user = $patient->user;

                // Guard: only attempt to write patient_id if the column exists (prevents SQL errors when migration not yet run)
                if (Schema::hasColumn('users', 'patient_id')) {
                    if ($user->patient_id !== $patient->id) {
                        $user->patient_id = $patient->id;
                        $user->save();
                    }
                }
            }
        });
    }
}
