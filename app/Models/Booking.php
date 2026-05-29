<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'salon_id',
        'booking_date',
        'booking_time',
        'service_type',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salon()
    {
        return $this->belongsTo(Barbershop::class, 'salon_id');
    }
}
