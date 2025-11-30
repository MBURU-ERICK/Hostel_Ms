<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'is_approved',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }



    // Add to your User model
public function isLandlord()
{
    return $this->user_type === 'landlord' && $this->hostels()->exists();
}


public function hostels()
{
    return $this->hasMany(Hostel::class, 'landlord_id');
}

    public function isServiceProvider(): bool
    {
        return $this->user_type === 'service_provider';
    }

    public function isAdmin(): bool
    {
        return $this->email === 'admin@hostel.com'; // Or use role-based system
    }
// Replace the existing methods with these simpler versions
public function hasBookedHostel($hostelId)
{
    return $this->bookings()
        ->where('hostel_id', $hostelId)
        ->where('booking_status', 'confirmed')
        ->exists();
}

public function getBookingForHostel($hostelId)
{
    return $this->bookings()
        ->where('hostel_id', $hostelId)
        ->where('booking_status', 'confirmed')
        ->first();
}

// Add to your User model
public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function favoriteHostels()
{
    return $this->belongsToMany(Hostel::class, 'favorites')
                ->withTimestamps();
}

public function hasFavorited($hostelId)
{
    return $this->favorites()->where('hostel_id', $hostelId)->exists();
}
// Add these relationships to your User model
public function serviceProvider()
{
    return $this->hasOne(ServiceProvider::class);
}

public function serviceRequests()
{
    return $this->hasMany(ServiceRequest::class, 'student_id');
}
}
