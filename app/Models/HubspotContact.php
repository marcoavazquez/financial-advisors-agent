<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubspotContact extends Model
{
    protected $fillable = [
        'user_id',
        'hubspot_id',
        'email',
        'first_name',
        'last_name',
        'company',
        'notes',
        'embedding',
    ];

    protected $casts = [
        // 'embedding' => 'vector',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
