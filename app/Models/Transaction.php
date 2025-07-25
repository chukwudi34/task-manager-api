<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount_paid',
        'status',
        'type',
        'plan_id',
        'raw_response'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
