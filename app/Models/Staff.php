<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'position',
        'department_id',
        'joining_date',
        'status',
        'address',
        'user_id',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];
    protected $hidden = [
        'password',
    ];

    public function departmentRelation()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    // In Staff.php model
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function upcomingShifts()
    {
        return $this->shifts()->where('shift_date', '>=', now()->format('Y-m-d'))->orderBy('shift_date');
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Create or attach a user when staff is being created
        static::creating(function ($staff) {
            if (!$staff->user_id) {
                $user = User::firstOrCreate(
                    ['email' => $staff->email],
                    [
                        'name' => $staff->name,
                        'email' => $staff->email,
                        'password' => $staff->password ?? Hash::make('password'),
                        'user_type' => 'staff',
                        'status' => $staff->status ?? 'active',
                        'phone' => $staff->phone,
                        'gender' => 'female',
                        'role_id' => Role::where('slug', 'staff')->first()->id ?? null,
                    ]
                );
                $staff->user_id = $user->id;
            }
        });

        // After staff is created, set the back-reference on the user to point to this staff
        static::created(function ($staff) {
            if ($staff->user && Schema::hasColumn('users', 'staff_id')) {
                $user = $staff->user;
                if ($user->staff_id !== $staff->id) {
                    $user->staff_id = $staff->id;
                    $user->save();
                }
            }
        });
    }
}
