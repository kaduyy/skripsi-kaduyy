<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stylist extends Model
{
    protected $fillable = ['nama', 'status'];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}