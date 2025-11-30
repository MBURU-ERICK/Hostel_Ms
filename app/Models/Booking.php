<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hostel_id',
        'check_in_date',
        'check_out_date',
        'duration_months',
        'total_amount',
        'amount_paid',
        'payment_status',
        'booking_status',
        'special_requests',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function hostel()
{
    return $this->belongsTo(Hostel::class);
}

public function student()
{
    return $this->belongsTo(User::class, 'user_id');
}

    // Calculate total amount based on duration
    public function calculateTotalAmount()
    {
        $hostel = $this->hostel;
        $rent = $hostel->rent_per_month;
        $deposit = $hostel->deposit_amount;

        return ($rent * $this->duration_months) + $deposit;
    }

    // Check if booking can be cancelled
    public function canBeCancelled()
    {
        return in_array($this->booking_status, ['pending', 'confirmed']) &&
               !in_array($this->payment_status, ['paid']);
    }

    // Get status badge color
    public function getStatusBadgeColor()
    {
        return match($this->booking_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
     public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the latest message for this booking
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Check if booking can have messages
     */
    public function canMessage()
    {
        return in_array($this->booking_status, ['pending', 'confirmed', 'approved']);
    }

    /**
     * Get unread messages count for current user
     */
    public function getUnreadMessagesCount()
    {
        return $this->messages()
                   ->where('receiver_id', auth()->id())
                   ->where('is_read', false)
                   ->count();
    }
}

