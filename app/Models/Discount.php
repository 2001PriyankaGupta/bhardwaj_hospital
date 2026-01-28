<?php
// app/Models/Discount.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'applicable_to',
        'applicable_ids',
        'valid_from',
        'valid_until',
        'usage_limit',
        'is_active'
    ];

    protected $casts = [
        'applicable_ids' => 'array',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2'
    ];
}