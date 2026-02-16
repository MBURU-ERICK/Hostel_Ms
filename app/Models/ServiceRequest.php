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
    'address',
    'room_number',
    'preferred_date',
    'estimated_cost',
    'actual_cost',
    'student_rating',
    'student_review',
    'rated_at',
    'notes'
];
    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'cost' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'student_rating' => 'integer',
        'notes' => 'array',
        'rated_at' => 'datetime'
    ];
    // Status options - updated to match controller
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned'; // This replaces STATUS_ACCEPTED
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    // Urgency levels
    const URGENCY_LOW = 'low';
    const URGENCY_MEDIUM = 'medium';
    const URGENCY_HIGH = 'high';
    const URGENCY_EMERGENCY = 'emergency';

    // Service types - expanded to match controller
      const TYPE_PLUMBING = 'plumbing';
    const TYPE_ELECTRICAL = 'electrical';
    const TYPE_CLEANING = 'cleaning';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_INTERNET = 'internet';
    const TYPE_PEST_CONTROL = 'pest_control';
    const TYPE_FURNITURE = 'furniture';
    const TYPE_SECURITY = 'security';
    const TYPE_OTHER = 'other';


    // Priority levels (for internal use)
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    // Relationships - updated to match new field names
        // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function serviceProviderDetail()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id', 'user_id');
    }

    public function serviceProviderUser()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function messages()
    {
        return $this->hasManyThrough(Message::class, Conversation::class);
    }

    public function getPrimaryConversationAttribute()
    {
        return $this->conversations()->first();
    }
    // Methods
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_ASSIGNED => 'bg-blue-100 text-blue-800',
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

    public function getServiceTypeBadgeClass()
    {
        return match($this->service_type) {
            self::TYPE_PLUMBING => 'bg-blue-100 text-blue-800',
            self::TYPE_ELECTRICAL => 'bg-yellow-100 text-yellow-800',
            self::TYPE_CLEANING => 'bg-green-100 text-green-800',
            self::TYPE_MAINTENANCE => 'bg-purple-100 text-purple-800',
            self::TYPE_INTERNET => 'bg-indigo-100 text-indigo-800',
            self::TYPE_PEST_CONTROL => 'bg-red-100 text-red-800',
            self::TYPE_FURNITURE => 'bg-brown-100 text-brown-800',
            self::TYPE_SECURITY => 'bg-gray-100 text-gray-800',
            self::TYPE_OTHER => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getServiceTypeNameAttribute()
    {
        return match($this->service_type) {
            self::TYPE_PLUMBING => 'Plumbing',
            self::TYPE_ELECTRICAL => 'Electrical',
            self::TYPE_CLEANING => 'Cleaning',
            self::TYPE_MAINTENANCE => 'Maintenance',
            self::TYPE_INTERNET => 'Internet',
            self::TYPE_PEST_CONTROL => 'Pest Control',
            self::TYPE_FURNITURE => 'Furniture Repair',
            self::TYPE_SECURITY => 'Security',
            self::TYPE_OTHER => 'Other',
            default => ucfirst($this->service_type)
        };
    }

    // NEW: Authorization method matching controller
    public function canUserAccess(User $user): bool
    {
        return $user->isAdmin() ||
               $this->student_id === $user->id ||
               $this->service_provider_id === $user->id ||
               ($user->isLandlord() && $this->hostel && $this->hostel->landlord_id === $user->id);
    }


    // Check if service request is related to a landlord
    private function isRelatedToLandlord(User $landlord): bool
    {
        // Check if the student has bookings in landlord's hostels
        return $this->user->bookings()
            ->whereHas('hostel', function($query) use ($landlord) {
                $query->where('landlord_id', $landlord->id);
            })
            ->exists();
    }

    // Check if user can modify service request
    public function canUserModify(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent() && $this->user_id === $user->id) {
            return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ASSIGNED]);
        }

        if ($user->isLandlord()) {
            return $this->isRelatedToLandlord($user);
        }

        if ($user->isServiceProvider() && $this->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    // Start or get conversation for this service request
    public function startConversation()
    {
        // If no assigned service provider, can't start conversation
        if (!$this->assigned_to) {
            return null;
        }

        $conversation = Conversation::where('service_request_id', $this->id)->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $this->user_id,
                'user2_id' => $this->assigned_to,
                'conversation_type' => 'service_request',
                'service_request_id' => $this->id,
                'last_message_at' => now(),
            ]);
        }

        return $conversation;
    }

    public function sendAutoMessage($message, $sender = null)
    {
        $conversation = $this->startConversation();

        if (!$conversation) {
            return null;
        }

        // If no sender specified, use system user or student
        $senderId = $sender ? $sender->id : $this->user_id;

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $senderId === $this->user_id ? $this->assigned_to : $this->user_id,
            'message' => $message,
            'is_read' => false,
            'message_type' => 'system'
        ]);
    }

    // Workflow methods that trigger messages
    public function markAsAssigned(User $assignedBy, User $serviceProvider)
    {
        $this->update([
            'status' => self::STATUS_ASSIGNED,
            'assigned_to' => $serviceProvider->id
        ]);

        $this->sendAutoMessage(
            "Your service request has been assigned to {$serviceProvider->name}. They will contact you soon.",
            $assignedBy
        );
    }

    public function markAsInProgress(User $updatedBy)
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);

        $this->sendAutoMessage(
            "Service work has started. The technician is now working on your request.",
            $updatedBy
        );
    }

    public function markAsCompleted(User $completedBy, $actualCost = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_date' => now(),
            'actual_cost' => $actualCost ?? $this->estimated_cost
        ]);

        $costMessage = $actualCost ? "Total cost: $" . number_format($actualCost, 2) : "";

        $this->sendAutoMessage(
            "Service request has been completed. " . $costMessage,
            $completedBy
        );
    }

    public function addUpdateMessage(User $sender, $message)
    {
        $conversation = $this->startConversation();

        if (!$conversation) {
            return null;
        }

        $receiverId = $sender->id === $this->user_id ? $this->assigned_to : $this->user_id;

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'message' => $message,
            'is_read' => false,
            'message_type' => 'update'
        ]);
    }

    // Add internal note
    public function addNote(User $user, $note)
    {
        $notes = $this->notes ?? [];
        $notes[] = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'note' => $note,
            'timestamp' => now()->toDateTimeString()
        ];

        $this->update(['notes' => $notes]);
    }

    // Rate service request
    public function rateService($rating, $review = null)
    {
        $this->update([
            'rating' => $rating,
            'review' => $review,
            'rated_at' => now()
        ]);

        // Update service provider rating
        if ($this->assigned_to) {
            $this->updateServiceProviderRating();
        }
    }

    // Update service provider's overall rating
    private function updateServiceProviderRating()
    {
        $serviceProvider = User::find($this->assigned_to);
        if (!$serviceProvider || !$serviceProvider->isServiceProvider()) {
            return;
        }

        $completedRequests = ServiceRequest::where('assigned_to', $this->assigned_to)
            ->whereNotNull('rating')
            ->get();

        if ($completedRequests->count() > 0) {
            $averageRating = $completedRequests->avg('rating');

            if ($serviceProvider->serviceProviderDetail) {
                $serviceProvider->serviceProviderDetail->update([
                    'rating' => round($averageRating, 1),
                    'total_ratings' => $completedRequests->count()
                ]);
            }
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForServiceProvider($query, $serviceProviderId)
    {
        return $query->where('service_provider_id', $serviceProviderId);
    }
    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', [self::URGENCY_HIGH, self::URGENCY_EMERGENCY]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeOfType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    // Accessors
    public function getFormattedEstimatedCostAttribute()
    {
        return $this->estimated_cost ? 'KSh ' . number_format($this->estimated_cost, 2) : 'Not estimated';
    }

    public function getFormattedActualCostAttribute()
    {
        return $this->actual_cost ? 'KSh ' . number_format($this->actual_cost, 2) : 'Not set';
    }

    public function getIsUrgentAttribute()
    {
        return in_array($this->urgency, [self::URGENCY_HIGH, self::URGENCY_EMERGENCY]);
    }

    public function getCanBeRatedAttribute()
    {
        return $this->status === self::STATUS_COMPLETED && !$this->rating;
    }

    public function getDurationAttribute()
    {
        if ($this->completed_date && $this->created_at) {
            return $this->created_at->diffInDays($this->completed_date);
        }
        return null;
    }

    public function getHasConversationAttribute()
    {
        return $this->conversation()->exists();
    }

    // Check if service request is overdue
    public function getIsOverdueAttribute()
    {
        if ($this->preferred_date && $this->status !== self::STATUS_COMPLETED) {
            return $this->preferred_date->isPast();
        }
        return false;
    }

    // Get service request priority based on urgency and age
    public function getPriorityAttribute()
    {
        if ($this->urgency === self::URGENCY_EMERGENCY) {
            return self::PRIORITY_HIGH;
        }

        if ($this->urgency === self::URGENCY_HIGH) {
            return self::PRIORITY_HIGH;
        }

        // If request is more than 7 days old, increase priority
        if ($this->created_at->diffInDays(now()) > 7) {
            return self::PRIORITY_MEDIUM;
        }

        return match($this->urgency) {
            self::URGENCY_MEDIUM => self::PRIORITY_MEDIUM,
            self::URGENCY_LOW => self::PRIORITY_LOW,
            default => self::PRIORITY_LOW
        };
    }
}
