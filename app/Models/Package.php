<?php
// app/Models/Package.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'included_services',
        'is_active'
    ];

    protected $casts = [
        'included_services' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];
}