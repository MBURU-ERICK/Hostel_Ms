<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'service_type',
        'description',
        'experience_years',
        'license_number',
        'is_verified',
        'is_available',
        'rating',
        'total_ratings',
        'response_time',
        'hourly_rate',
        'coverage_areas',
        'total_jobs_completed'
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'rating' => 'decimal:1',
        'total_ratings' => 'integer',
        'response_time' => 'integer',
        'hourly_rate' => 'decimal:2',
        'coverage_areas' => 'array',
        'total_jobs_completed' => 'integer'
    ];

    // Service types constants
    const TYPE_WIFI = 'wifi_installation';
    const TYPE_PLUMBING = 'plumbing';
    const TYPE_ELECTRICAL = 'electrical';
    const TYPE_SEWAGE = 'sewage';
    const TYPE_CARPENTRY = 'carpentry';
    const TYPE_CLEANING = 'cleaning';
    const TYPE_PEST_CONTROL = 'pest_control';
    const TYPE_OTHER = 'other';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'service_provider_id', 'user_id');
    }

    // NEW: Messaging Relationships
    public function conversations()
    {
        return $this->hasManyThrough(
            Conversation::class,
            ServiceRequest::class,
            'service_provider_id', // Foreign key on service_requests table
            'service_request_id'   // Foreign key on conversations table
        );
    }

    public function directConversations()
    {
        return Conversation::where(function($query) {
            $query->where('user1_id', $this->user_id)
                  ->orWhere('user2_id', $this->user_id);
        })->where('conversation_type', 'direct');
    }

    // Methods
    public function serviceTypes()
    {
        return [
            self::TYPE_WIFI => 'WiFi Installation',
            self::TYPE_PLUMBING => 'Plumbing & Water Leakage',
            self::TYPE_ELECTRICAL => 'Electrical Repairs',
            self::TYPE_SEWAGE => 'Sewage & Drainage',
            self::TYPE_CARPENTRY => 'Carpentry & Furniture',
            self::TYPE_CLEANING => 'Deep Cleaning',
            self::TYPE_PEST_CONTROL => 'Pest Control',
            self::TYPE_OTHER => 'Other Maintenance'
        ];
    }

    public function getServiceTypeNameAttribute()
    {
        return $this->serviceTypes()[$this->service_type] ?? 'Unknown';
    }

    public function getServiceTypeBadgeClass()
    {
        return match($this->service_type) {
            self::TYPE_WIFI => 'bg-blue-100 text-blue-800',
            self::TYPE_PLUMBING => 'bg-indigo-100 text-indigo-800',
            self::TYPE_ELECTRICAL => 'bg-yellow-100 text-yellow-800',
            self::TYPE_SEWAGE => 'bg-orange-100 text-orange-800',
            self::TYPE_CARPENTRY => 'bg-amber-100 text-amber-800',
            self::TYPE_CLEANING => 'bg-green-100 text-green-800',
            self::TYPE_PEST_CONTROL => 'bg-lime-100 text-lime-800',
            self::TYPE_OTHER => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // NEW: Messaging and Business Logic Methods
    public function canHandleServiceRequest(ServiceRequest $serviceRequest): bool
    {
        return $this->service_type === $serviceRequest->service_type &&
               $this->is_verified &&
               $this->is_available;
    }

    public function getActiveServiceRequests()
    {
        return $this->serviceRequests()
            ->whereIn('status', [
                ServiceRequest::STATUS_ASSIGNED, // CHANGED: STATUS_ACCEPTED to STATUS_ASSIGNED
                ServiceRequest::STATUS_IN_PROGRESS
            ])
            ->get();
    }

    public function getCompletedServiceRequests()
    {
        return $this->serviceRequests()
            ->where('status', ServiceRequest::STATUS_COMPLETED)
            ->get();
    }

    public function getPendingServiceRequests()
    {
        return $this->serviceRequests()
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->get();
    }

    public function calculateAverageRating()
    {
        $completedRequests = $this->serviceRequests()
            ->where('status', ServiceRequest::STATUS_COMPLETED)
            ->whereNotNull('student_rating')
            ->get();

        if ($completedRequests->isEmpty()) {
            return 0;
        }

        return $completedRequests->avg('student_rating');
    }

    public function updateRating()
    {
        $averageRating = $this->calculateAverageRating();
        $completedJobs = $this->getCompletedServiceRequests()->count();

        $this->update([
            'rating' => $averageRating,
            'total_ratings' => $completedJobs,
            'total_jobs_completed' => $completedJobs
        ]);

        return $this;
    }

    public function getResponseTimeText()
    {
        if (!$this->response_time) {
            return 'Not specified';
        }

        if ($this->response_time <= 2) {
            return 'Within 2 hours';
        } elseif ($this->response_time <= 6) {
            return 'Within 6 hours';
        } elseif ($this->response_time <= 24) {
            return 'Within 24 hours';
        } else {
            return 'More than 24 hours';
        }
    }

    public function getFormattedHourlyRate()
    {
        if (!$this->hourly_rate) {
            return 'Not specified';
        }
        return 'KSh ' . number_format($this->hourly_rate, 2) . '/hour';
    }

    public function getCoverageAreasList()
    {
        if (empty($this->coverage_areas)) {
            return 'Not specified';
        }

        return is_array($this->coverage_areas)
            ? implode(', ', $this->coverage_areas)
            : $this->coverage_areas;
    }

    // Check if service provider can accept new requests
    public function canAcceptNewRequests(): bool
    {
        $activeRequests = $this->getActiveServiceRequests()->count();

        // Limit to 5 active requests at a time
        return $this->is_available && $this->is_verified && $activeRequests < 5;
    }

    // Get available service providers for a specific service type
    public static function getAvailableForServiceType($serviceType)
    {
        return static::where('service_type', $serviceType)
            ->where('is_verified', true)
            ->where('is_available', true)
            ->orderBy('rating', 'desc')
            ->orderBy('total_jobs_completed', 'desc')
            ->get();
    }

    // Get recommended service providers for a student based on location and rating
    public static function getRecommendedProviders($serviceType, $studentLocation = null)
    {
        $query = static::where('service_type', $serviceType)
            ->where('is_verified', true)
            ->where('is_available', true)
            ->orderBy('rating', 'desc')
            ->orderBy('total_jobs_completed', 'desc');

        // If location is provided, filter by coverage areas
        if ($studentLocation) {
            $query->where(function($q) use ($studentLocation) {
                $q->whereJsonContains('coverage_areas', $studentLocation)
                  ->orWhereNull('coverage_areas')
                  ->orWhere('coverage_areas', '[]');
            });
        }

        return $query->get();
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeByRating($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeExperienced($query, $minYears = 3)
    {
        return $query->where('experience_years', '>=', $minYears);
    }

    public function scopeAffordable($query, $maxHourlyRate = 50)
    {
        return $query->where('hourly_rate', '<=', $maxHourlyRate);
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->company_name ?: $this->user->name;
    }

    public function getIsHighlyRatedAttribute()
    {
        return $this->rating >= 4.5;
    }

    public function getIsExperiencedAttribute()
    {
        return $this->experience_years >= 5;
    }

    public function getSuccessRateAttribute()
    {
        $totalRequests = $this->serviceRequests()->count();
        $completedRequests = $this->getCompletedServiceRequests()->count();

        if ($totalRequests === 0) {
            return 0;
        }

        return round(($completedRequests / $totalRequests) * 100);
    }

    // NEW: Messaging-specific methods
    public function getMessagingClients()
    {
        // Get all unique clients (students) this service provider has worked with
        return User::whereIn('id', function($query) {
            $query->select('student_id')
                  ->from('service_requests')
                  ->where('service_provider_id', $this->user_id); // CHANGED: $this->id to $this->user_id
        })->get();
    }

    public function getUnreadMessagesCount()
    {
        return Message::where('receiver_id', $this->user_id)
            ->where('is_read', false)
            ->count();
    }

    public function sendMessageToClient(User $client, $message)
    {
        if (!$client->isStudent()) {
            return null;
        }

        // Find or create conversation
        $conversation = Conversation::where(function($query) use ($client) {
            $query->where('user1_id', $this->user_id)
                  ->where('user2_id', $client->id);
        })->orWhere(function($query) use ($client) {
            $query->where('user1_id', $client->id)
                  ->where('user2_id', $this->user_id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $this->user_id,
                'user2_id' => $client->id,
                'conversation_type' => 'direct',
                'last_message_at' => now(),
            ]);
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user_id,
            'receiver_id' => $client->id,
            'message' => $message,
            'is_read' => false,
        ]);

        // Update conversation timestamp
        $conversation->update(['last_message_at' => now()]);

        return $message;
    }

    // NEW: Get service provider statistics
    public function getStats()
    {
        return [
            'total_requests' => $this->serviceRequests()->count(),
            'pending_requests' => $this->getPendingServiceRequests()->count(),
            'active_jobs' => $this->getActiveServiceRequests()->count(),
            'completed_jobs' => $this->getCompletedServiceRequests()->count(),
            'total_earnings' => $this->serviceRequests()
                ->where('status', ServiceRequest::STATUS_COMPLETED)
                ->sum('cost'),
            'average_rating' => $this->calculateAverageRating()
        ];
    }

    // NEW: Check if service provider is assigned to a specific service request
    public function isAssignedTo(ServiceRequest $serviceRequest): bool
    {
        return $this->user_id === $serviceRequest->service_provider_id;
    }

    // NEW: Accept a service request
    public function acceptServiceRequest(ServiceRequest $serviceRequest)
    {
        if (!$this->canHandleServiceRequest($serviceRequest)) {
            return false;
        }

        $serviceRequest->update([
            'service_provider_id' => $this->user_id,
            'status' => ServiceRequest::STATUS_ASSIGNED
        ]);

        return true;
    }
}
