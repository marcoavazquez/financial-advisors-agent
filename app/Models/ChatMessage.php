<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'thread_id',
        'content',
        'is_assistant',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
