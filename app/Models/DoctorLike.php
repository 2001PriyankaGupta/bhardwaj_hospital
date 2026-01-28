<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorLike extends Model
{
    protected $fillable = ['user_id', 'doctor_id', 'is_liked'];
    
    protected $casts = [
        'is_liked' => 'boolean'
    ];
    
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}