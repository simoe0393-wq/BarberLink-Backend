<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barbershop extends Model
{
    protected $fillable = [
        'owner_id',
        'salon_name',
        'address',
        'qr_token',
        'qr_image_path',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
