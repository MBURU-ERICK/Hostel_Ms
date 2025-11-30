<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'service_provider_id',
        'hostel_id',
        'service_type',
        'title',
        'description',
        'urgency_level',
        'status',
        'scheduled_date',
        'completed_date',
        'student_rating',
        'student_review',
        'cost',
        'address',
        'room_number'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'cost' => 'decimal:2',
        'student_rating' => 'integer'
    ];

    // Status options
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Urgency levels
    const URGENCY_LOW = 'low';
    const URGENCY_MEDIUM = 'medium';
    const URGENCY_HIGH = 'high';
    const URGENCY_EMERGENCY = 'emergency';

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_ACCEPTED => 'bg-blue-100 text-blue-800',
            self::STATUS_IN_PROGRESS => 'bg-purple-100 text-purple-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getUrgencyBadgeClass()
    {
        return match($this->urgency_level) {
            self::URGENCY_LOW => 'bg-green-100 text-green-800',
            self::URGENCY_MEDIUM => 'bg-yellow-100 text-yellow-800',
            self::URGENCY_HIGH => 'bg-orange-100 text-orange-800',
            self::URGENCY_EMERGENCY => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
