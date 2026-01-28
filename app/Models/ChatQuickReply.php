<?php

// app/Models/ChatQuickReply.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatQuickReply extends Model
{
    protected $fillable = [
        'title',
        'message',
        'reply_type',
        'action_type',
        'icon',
        'display_order',
        'is_active'
    ];
}
