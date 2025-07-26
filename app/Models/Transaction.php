<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'type',
        'plan_id',
        'raw_response',
        'subscriber_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
