<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'salon_id',
        'amount',
        'proof_image',
        'payment_status',
    ];

    public function salon()
    {
        return $this->belongsTo(Barbershop::class, 'salon_id');
    }
}
