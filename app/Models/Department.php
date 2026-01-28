<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'head_id',
        'email',
        'phone',
        'display_order',
        'is_active',
        'services'
    ];

    protected $casts = [
        'services' => 'array',
        'is_active' => 'boolean'
    ];

    // Self-referential relationship for hierarchy
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id')->orderBy('display_order');
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    

    // Scope for active departments
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get all descendants
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Get hierarchy tree
    public static function getTree()
    {
        return static::with('descendants')->whereNull('parent_id')->orderBy('display_order')->get();
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'department_id');
    }
}