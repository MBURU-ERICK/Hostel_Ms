<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'transaction_id',
        'amount',
        'phone_number',
        'payment_method',
        'status',
        'merchant_request_id',
        'checkout_request_id',
        'response_code',
        'result_description',
        'result_code',
        'initiated_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Get status badge color
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'successful' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Check if payment is successful
    public function isSuccessful()
    {
        return $this->status === 'successful';
    }

    // Check if payment can be retried
   // In app/Models/Payment.php
public function canRetry()
{
    return in_array($this->status, ['failed', 'cancelled']) &&
           $this->created_at->gt(now()->subHours(24));
}
}
