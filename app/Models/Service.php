<?php
// app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function rateCards()
    {
        return $this->hasMany(RateCard::class);
    }
}