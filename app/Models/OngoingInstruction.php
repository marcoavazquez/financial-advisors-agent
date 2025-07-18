<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OngoingInstruction extends Model
{
    protected $fillable = [
        'user_id',
        'instruction',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
