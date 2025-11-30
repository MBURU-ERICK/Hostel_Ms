<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'name',
        'description',
        'location',
        'address',
        'rent_per_month',
        'deposit_amount',
        'rooms_available',
        'total_rooms',
        'amenities',
        'rules',
        'is_approved',
        'is_available',
        'images',
        'contact_phone',
        'contact_email',
    ];

    protected $casts = [
    'amenities' => 'array', // This ensures it's always treated as an array
    'images' => 'array',
    'is_approved' => 'boolean',
    'is_available' => 'boolean',
    'rent_per_month' => 'decimal:2',
    'deposit_amount' => 'decimal:2',
];
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }
        public function bookings()
    {
        return $this->hasMany(Booking::class);
    }


    // Scope for approved hostels
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Scope for available hostels
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('rooms_available', '>', 0);
    }

    // Search scope
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    // Price range scope
    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        if ($minPrice) {
            $query->where('rent_per_month', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('rent_per_month', '<=', $maxPrice);
        }
        return $query;
    }

    // Location scope
    public function scopeByLocation($query, $location)
    {
        if ($location) {
            return $query->where('location', 'like', "%{$location}%");
        }
        return $query;
    }
    // Add this to your Hostel model
public function reviews()
{
    return $this->hasMany(Review::class);
}

public function approvedReviews()
{
    return $this->hasMany(Review::class)->approved();
}

public function getAverageRatingAttribute()
{
    return $this->approvedReviews()->avg('rating') ?? 0;
}

public function getTotalReviewsAttribute()
{
    return $this->approvedReviews()->count();
}

    public function getRatingBreakdownAttribute()
    {
        return $this->reviews()
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->rating => $item->count];
            });
    }
// Add to your Hostel model
public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function favoritedBy()
{
    return $this->belongsToMany(User::class, 'favorites')
                ->withTimestamps();
}

public function getIsFavoritedAttribute()
{
    if (!auth()->check()) {
        return false;
    }
    
    return $this->favorites()->where('user_id', auth()->id())->exists();
}

public function getFavoritesCountAttribute()
{
    return $this->favorites()->count();
}
}
