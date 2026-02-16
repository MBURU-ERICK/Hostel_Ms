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
    'is_active',
    // Landlord specific fields
    'id_number',
    'address',
    'county',
    'constituency',
    'town',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Existing relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function hostels()
    {
        return $this->hasMany(Hostel::class, 'landlord_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteHostels()
    {
        return $this->belongsToMany(Hostel::class, 'favorites')
                    ->withTimestamps();
    }


public function serviceProvider()
{
    return $this->hasOne(ServiceProvider::class, 'user_id');
}

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'student_id');
    }

    // NEW: Messaging Relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    // Get all conversations for this user
    public function conversations()
    {
        return Conversation::where(function($query) {
            $query->where('user1_id', $this->id)
                  ->orWhere('user2_id', $this->id);
        });
    }

    // Get unread messages count
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    // Get conversations with unread messages
    public function conversationsWithUnread()
    {
        return $this->conversations()
            ->whereHas('messages', function($query) {
                $query->where('receiver_id', $this->id)
                      ->where('is_read', false);
            })
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('receiver_id', $this->id)
                      ->where('is_read', false);
            }]);
    }

    // User type checks
    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    public function isLandlord(): bool
    {
        return $this->user_type === 'landlord';
    }

    public function isServiceProvider(): bool
    {
        return $this->user_type === 'service_provider';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    // Business logic methods
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

    public function hasFavorited($hostelId)
    {
        return $this->favorites()->where('hostel_id', $hostelId)->exists();
    }

    public function canAccessSystem(): bool
    {
        return $this->is_approved && $this->is_active;
    }

    public function isSuspended(): bool
    {
        return !$this->is_active;
    }

    public function isPendingApproval(): bool
    {
        return !$this->is_approved;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_active', false);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    // NEW: Messaging helper methods
    public function canMessage(User $recipient): bool
    {
        // Admin can message anyone
        if ($this->isAdmin()) {
            return true;
        }

        // Users can't message themselves
        if ($this->id === $recipient->id) {
            return false;
        }

        // All active, approved users can message each other
        return $this->canAccessSystem() && $recipient->canAccessSystem();
    }

    public function getConversationWith(User $otherUser)
    {
        return Conversation::where(function($query) use ($otherUser) {
            $query->where('user1_id', $this->id)
                  ->where('user2_id', $otherUser->id);
        })->orWhere(function($query) use ($otherUser) {
            $query->where('user1_id', $otherUser->id)
                  ->where('user2_id', $this->id);
        })->first();
    }

    public function startConversationWith(User $otherUser, $conversationType = 'direct', $bookingId = null, $serviceRequestId = null)
    {
        $conversation = $this->getConversationWith($otherUser);

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $this->id,
                'user2_id' => $otherUser->id,
                'conversation_type' => $conversationType,
                'booking_id' => $bookingId,
                'service_request_id' => $serviceRequestId,
                'last_message_at' => now(),
            ]);
        }

        return $conversation;
    }

    // Get display name for messaging
    public function getDisplayName(): string
    {
        if ($this->isServiceProvider() && $this->serviceProvider) {
            return $this->serviceProvider->company_name ?: $this->name;
        }

        return $this->name;
    }

    // Get user badge/role for display
    public function getUserBadge(): string
    {
        switch ($this->user_type) {
            case 'student':
                return 'Student';
            case 'landlord':
                return 'Landlord';
            case 'service_provider':
                return 'Service Provider';
            case 'admin':
                return 'Administrator';
            default:
                return 'User';
        }
    }

    // Get available users for messaging based on user type
    public function getAvailableMessagingUsers()
    {
        $query = User::where('id', '!=', $this->id)
            ->active()
            ->approved();

        // Different user types can message different people
        if ($this->isStudent()) {
            // Students can message landlords and service providers
            $query->whereIn('user_type', ['landlord', 'service_provider']);
        } elseif ($this->isLandlord()) {
            // Landlords can message students and service providers
            $query->whereIn('user_type', ['student', 'service_provider']);
        } elseif ($this->isServiceProvider()) {
            // Service providers can message students and landlords
            $query->whereIn('user_type', ['student', 'landlord']);
        } elseif ($this->isAdmin()) {
            // Admin can message everyone
            // No additional filters needed
        }

        return $query->get();
    }
    // Add to your User model
public function serviceProviderDetail()
{
    return $this->hasOne(ServiceProvider::class);
}

public function getServiceProviderAttribute()
{
    return $this->serviceProviderDetail;
}
}
