<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope for recent notifications
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    // Mark all as read for user
    public static function markAllAsRead($userId)
    {
        return static::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    // Get notification icon based on type
    public function getIcon()
    {
        return match($this->type) {
            'booking_created' => '📅',
            'booking_confirmed' => '✅',
            'booking_cancelled' => '❌',
            'payment_received' => '💰',
            'payment_failed' => '💳',
            'message_received' => '💬',
            'system_alert' => '⚠️',
            default => '🔔',
        };
    }

    // Get notification color based on type
    public function getColor()
    {
        return match($this->type) {
            'booking_created' => 'blue',
            'booking_confirmed' => 'green',
            'booking_cancelled' => 'red',
            'payment_received' => 'green',
            'payment_failed' => 'red',
            'message_received' => 'indigo',
            'system_alert' => 'yellow',
            default => 'gray',
        };
    }
}
