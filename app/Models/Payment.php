<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'units_paid', 'mpesa_transaction_id', 'status', 'failure_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}