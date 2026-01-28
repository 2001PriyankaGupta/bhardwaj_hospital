<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'organizer',
        'contact_person',
        'contact_number',
        'email',
        'target_participants',
        'registered_participants',
        'social_media_links',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'website_url',
        'requirements',
        'image',
        'status',
        'is_featured',
        'is_published'
    ];

    protected $casts = [
        'event_date' => 'date',
        'social_media_links' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean'
    ];

    public function getEventDateTimeAttribute()
    {
        return $this->event_date->format('Y-m-d') . ' ' . $this->start_time;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                    ->where('event_date', '>=', now())
                    ->orderBy('event_date', 'asc');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}