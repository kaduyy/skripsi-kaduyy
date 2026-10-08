<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $guarded = ['id'];

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reservedBookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_slot')->withTimestamps();
    }
}
