<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'user_id',
        'google_event_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'attendees',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'attendees' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
