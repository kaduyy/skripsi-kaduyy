<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'kategori',
        'deskripsi',
        'foto',
        'harga',
        'durasi_menit',
        'status',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_service')
            ->withPivot(['harga', 'durasi_menit'])
            ->withTimestamps();
    }

    public function bookingItems()
    {
        return $this->bookings();
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function reviews()
    {
        return $this->belongsToMany(Review::class, 'booking_service', 'service_id', 'booking_id', 'id', 'booking_id');
    }
}
