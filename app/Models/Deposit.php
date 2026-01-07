<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'cashier_id',
        'depositor_name',
        'amount',
        'payment_method',
        'deposit_date',
        'description',
    ];

    // Deposit belongs to a user (admin or cashier)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
