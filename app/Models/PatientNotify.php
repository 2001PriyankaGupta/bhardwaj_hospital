<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientNotify extends Model
{
    use HasFactory;

    protected $table = 'patient_notify';

    protected $fillable = [
        'patient_id',
        'title',
        'message',
    ];
}