<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_service')
            ->withPivot(['harga', 'durasi_menit'])
            ->withTimestamps();
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function reservedSlots()
    {
        return $this->belongsToMany(Slot::class, 'booking_slot')->withTimestamps();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function transactionBookings()
    {
        $bookings = $this->hasMany(self::class, 'transaction_group_id', 'transaction_group_id');

        if (! $this->transaction_group_id) {
            $bookings->whereKey($this->getKey());
        }

        return $bookings;
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function whatsappLogs()
    {
        return $this->hasMany(WhatsappLog::class);
    }
}
