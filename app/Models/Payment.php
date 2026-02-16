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
    'amount' => 'float', // Or remove casting and handle formatting in accessor
    'initiated_at' => 'datetime',
    'completed_at' => 'datetime',
];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_METHOD_MPESA = 'mpesa';

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessors & Mutators
     */
    public function getFormattedAmountAttribute()
    {
        return 'KSh ' . number_format($this->amount, 2);
    }

    public function getFormattedPhoneAttribute()
    {
        if (str_starts_with($this->phone_number, '254')) {
            return '+254 ' . substr($this->phone_number, 3, 3) . ' ' . substr($this->phone_number, 6);
        }
        return $this->phone_number;
    }

    public function getDurationAttribute()
    {
        if ($this->initiated_at && $this->completed_at) {
            return $this->initiated_at->diffForHumans($this->completed_at, true);
        }
        return null;
    }

    /**
     * Status Methods
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::STATUS_SUCCESSFUL => 'bg-green-100 text-green-800 border-green-200',
            self::STATUS_FAILED => 'bg-red-100 text-red-800 border-red-200',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            self::STATUS_PENDING => '⏳',
            self::STATUS_SUCCESSFUL => '✅',
            self::STATUS_FAILED => '❌',
            self::STATUS_CANCELLED => '🚫',
            default => '❓',
        };
    }

    public function isSuccessful()
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Business Logic Methods
     */
    public function canRetry()
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED]) &&
               $this->created_at->gt(now()->subHours(24)) &&
               $this->booking &&
               !$this->booking->isPaid();
    }

    public function markAsSuccessful($transactionId = null)
    {
        $this->update([
            'status' => self::STATUS_SUCCESSFUL,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'completed_at' => now(),
        ]);

        // Update booking status if payment is successful
        if ($this->booking) {
            $this->booking->update([
                'payment_status' => 'paid',
                'booking_status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'result_description' => $reason ?? $this->result_description,
            'completed_at' => now(),
        ]);
    }

    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'result_description' => $reason ?? $this->result_description,
            'completed_at' => now(),
        ]);
    }

    /**
     * Validation Methods
     */
    public function isValidForProcessing()
    {
        return $this->isPending() &&
               $this->initiated_at &&
               $this->initiated_at->gt(now()->subHours(2)); // Payments expire after 2 hours
    }

    /**
     * Static Helpers
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUCCESSFUL => 'Successful',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_METHOD_MPESA => 'M-Pesa',
        ];
    }

    /**
     * Statistics
     */
    public static function getTotalRevenue($days = null)
    {
        $query = self::successful();

        if ($days) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        return $query->sum('amount');
    }

    public static function getSuccessRate($days = 30)
    {
        $total = self::recent($days)->count();
        $successful = self::successful()->recent($days)->count();

        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }
}
