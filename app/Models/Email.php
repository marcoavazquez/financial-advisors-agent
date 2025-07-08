<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'user_id',
        'gmail_id',
        'subject',
        'from_email',
        'from_name',
        'to_email',
        'body',
        'embedding',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeContext($query, )
    {
        $query->orderByRaw('embedding <=> desc');
    }
}
