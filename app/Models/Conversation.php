<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'conversation_type',
        'booking_id',
        'service_request_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Derived Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the other participant in the conversation (not the authenticated user).
     */
    public function getOtherUserAttribute()
    {
        $currentUserId = auth()->id();
        return $currentUserId === $this->user1_id ? $this->user2 : $this->user1;
    }

    /**
     * Count unread messages for the authenticated user.
     */
    public function getUnreadCountAttribute()
    {
        return $this->messages()
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    /**
     * Returns the latest message in the conversation.
     */
    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Returns both participants' names (for admin display, logs, etc.)
     */
    public function getParticipantNames(): string
    {
        return trim(($this->user1->name ?? 'Unknown') . ' & ' . ($this->user2->name ?? 'Unknown'));
    }

    /**
     * Check if the conversation has any unread messages for the logged-in user.
     */
    public function hasUnreadMessages(): bool
    {
        return $this->unread_count > 0;
    }

    /**
     * Mark all messages in this conversation as read for the given user.
     */
    public function markMessagesAsRead(User $user)
    {
        $this->messages()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Role-based Participant Accessors
    |--------------------------------------------------------------------------
    |
    | These help you identify who is who in the conversation. Useful if a landlord
    | is chatting with a student, service provider, or admin.
    |
    */

    public function getStudentAttribute()
    {
        return $this->user1?->user_type === 'student'
            ? $this->user1
            : ($this->user2?->user_type === 'student' ? $this->user2 : null);
    }

    public function getServiceProviderAttribute()
    {
        return $this->user1?->user_type === 'service_provider'
            ? $this->user1
            : ($this->user2?->user_type === 'service_provider' ? $this->user2 : null);
    }

    public function getLandlordAttribute()
    {
        return $this->user1?->user_type === 'landlord'
            ? $this->user1
            : ($this->user2?->user_type === 'landlord' ? $this->user2 : null);
    }

    public function getAdminAttribute()
    {
        return $this->user1?->user_type === 'admin'
            ? $this->user1
            : ($this->user2?->user_type === 'admin' ? $this->user2 : null);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForUser($query, $userId)
    {
        return $query->where('user1_id', $userId)
                     ->orWhere('user2_id', $userId);
    }

    public function scopeWithUnreadMessages($query, $userId)
    {
        return $query->whereHas('messages', function ($q) use ($userId) {
            $q->where('receiver_id', $userId)
              ->where('is_read', false);
        });
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('last_message_at', '>=', now()->subDays($days));
    }
}
