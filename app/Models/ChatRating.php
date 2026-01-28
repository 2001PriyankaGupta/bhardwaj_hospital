<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRating extends Model
{
    protected $table = 'chat_ratings';

    protected $fillable = [
        'conversation_id',
        'patient_id',
        'rating',
        'feedback'
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id', 'conversation_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
