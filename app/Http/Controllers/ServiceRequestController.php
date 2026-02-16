<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Hostel;
use App\Models\ServiceProvider;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ServiceRequestController extends Controller
{
    /**
     * Display a listing of service requests based on user type
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ServiceRequest::with(['student', 'serviceProvider', 'hostel', 'conversation']);

        switch ($user->user_type) {
            case 'student':
                $query->where('student_id', $user->id);
                $title = 'My Service Requests';
                break;

            case 'landlord':
                // Landlords can see service requests related to their hostels
                $query->whereHas('student.bookings.hostel', function($q) use ($user) {
                    $q->where('landlord_id', $user->id);
                });
                $title = 'Tenant Service Requests';
                break;

            case 'service_provider':
                // Service providers see requests assigned to them or in their service type
                $query->where(function($q) use ($user) {
                    $q->where('service_provider_id', $user->id)
                      ->orWhere('service_type', $user->serviceProviderDetail->service_type ?? 'general');
                });
                $title = 'Service Requests';
                break;

            case 'admin':
                // Admins see all requests
                $title = 'All Service Requests';
                break;

            default:
                abort(403, 'Unauthorized access.');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service type
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by urgency
        if ($request->filled('urgency_level')) {
            $query->where('urgency_level', $request->urgency_level);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('service_type', 'like', "%{$search}%")
                  ->orWhereHas('student', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('service-requests.index', compact('serviceRequests', 'title'));
    }

/**
     * Display list of service requests for student
     */
    public function studentIndex(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403);
        }
        
        // Get student's service requests
        $myRequests = ServiceRequest::with(['hostel', 'serviceProvider'])
            ->where('student_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get available service providers for display
        $services = ServiceProvider::with('user')
            ->where('is_verified', true)
            ->where('is_available', true)
            ->get();
        
        return view('student.services.index', compact('myRequests', 'services'));
    }

    /**
     * Show form to create new service request for student
     */
    public function studentCreate()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403);
        }
        
        // Get student's active bookings/hostels
        $hostels = Hostel::whereHas('bookings', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->whereIn('booking_status', ['confirmed', 'active']);
        })->get();
        
        // If no bookings found, get all available hostels
        if ($hostels->isEmpty()) {
            $hostels = Hostel::approved()->available()->take(10)->get();
        }
        
        // Define service types
        $serviceTypes = [
            'plumbing' => 'Plumbing',
            'electrical' => 'Electrical',
            'carpentry' => 'Carpentry',
            'painting' => 'Painting',
            'cleaning' => 'Cleaning',
            'appliance_repair' => 'Appliance Repair',
            'furniture_repair' => 'Furniture Repair',
            'pest_control' => 'Pest Control',
            'other' => 'Other'
        ];
        
        return view('student.services.create', compact('hostels', 'serviceTypes'));
    }

    /**
     * Store a newly created service request in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can create service requests.');
        }

        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'hostel_id' => 'required|exists:hostels,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'urgency_level' => 'required|in:low,medium,high,emergency',
            'address' => 'required|string|max:500',
            'room_number' => 'required|string|max:50'
        ]);

        try {
            DB::beginTransaction();

            // Create service request
            $serviceRequest = ServiceRequest::create([
                'student_id' => $user->id,
                'hostel_id' => $validated['hostel_id'],
                'service_type' => $validated['service_type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'urgency_level' => $validated['urgency_level'],
                'address' => $validated['address'],
                'room_number' => $validated['room_number'],
                'status' => ServiceRequest::STATUS_PENDING,
                'service_provider_id' => null,
                'preferred_date' => null,
                'estimated_cost' => null,
                'actual_cost' => null,
                'notes' => null
            ]);

            // Notify admins and landlords about the new request
            $this->notifyServiceRequestCreation($serviceRequest);

            DB::commit();

            return redirect()->route('student.services.index')
                ->with('success', 'Service request submitted successfully! You will be contacted soon by a service provider.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create service request: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to submit service request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Rate a completed service (AJAX endpoint)
     */
    public function rateService(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        if ($serviceRequest->student_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($serviceRequest->status !== ServiceRequest::STATUS_COMPLETED) {
            return response()->json(['error' => 'Can only rate completed service requests.'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $serviceRequest->update([
            'student_rating' => $request->rating,
            'student_review' => $request->review,
            'rated_at' => now()
        ]);

        // Update service provider's average rating
        if ($serviceRequest->service_provider_id) {
            $this->updateServiceProviderRating($serviceRequest->service_provider_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    }
    /**
     * Display the specified service request
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // Check authorization based on user type
        if (!$this->canAccessServiceRequest($user, $serviceRequest)) {
            abort(403, 'Unauthorized access to service request.');
        }

        $conversation = $serviceRequest->conversation;
        $messages = $conversation ? $conversation->messages()->with('sender')->get() : collect();

        // Available service providers for assignment (for admins/landlords)
        $availableProviders = [];
        if ($user->isAdmin() || $user->isLandlord()) {
            $availableProviders = User::where('user_type', 'service_provider')
                ->whereHas('serviceProviderDetail', function($query) use ($serviceRequest) {
                    $query->where('is_verified', true)
                          ->where('is_available', true)
                          ->where('service_type', $serviceRequest->service_type);
                })
                ->with('serviceProviderDetail')
                ->get();
        }

        return view('service-requests.show', compact(
            'serviceRequest',
            'messages',
            'conversation',
            'availableProviders'
        ));
    }

    /**
     * Update the specified service request
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // Check authorization
        if (!$this->canModifyServiceRequest($user, $serviceRequest)) {
            abort(403, 'Unauthorized to modify service request.');
        }

        $validationRules = [];

        // Students can only update certain fields
        if ($user->isStudent()) {
            $validationRules = [
                'description' => 'sometimes|required|string|max:1000',
                'preferred_date' => 'sometimes|nullable|date|after:today',
                'address' => 'sometimes|nullable|string|max:500'
            ];
        }

        // Admins, landlords, and service providers can update more fields
        if ($user->isAdmin() || $user->isLandlord() || $user->isServiceProvider()) {
            $validationRules = array_merge($validationRules, [
                'status' => 'sometimes|required|in:pending,assigned,in_progress,completed,cancelled',
                'service_provider_id' => 'sometimes|nullable|exists:users,id', // Use service_provider_id
                'priority' => 'sometimes|in:low,medium,high',
                'estimated_cost' => 'sometimes|nullable|numeric|min:0',
                'actual_cost' => 'sometimes|nullable|numeric|min:0',
                'notes' => 'sometimes|nullable|string|max:1000'
            ]);
        }

        $request->validate($validationRules);

        $updateData = $request->only([
            'description', 'status', 'service_provider_id', 'priority', // Use service_provider_id
            'estimated_cost', 'actual_cost', 'notes', 'preferred_date', 'address' // Use address
        ]);

        // Handle assignment notifications
        if ($request->has('service_provider_id') && $request->service_provider_id != $serviceRequest->service_provider_id) {
            $this->notifyAssignment($serviceRequest, $request->service_provider_id);
        }

        // Handle status change notifications
        if ($request->has('status') && $request->status != $serviceRequest->status) {
            $this->notifyStatusChange($serviceRequest, $request->status);
        }

        $serviceRequest->update($updateData);

        return redirect()->back()->with('success', 'Service request updated successfully!');
    }

    /**
     * Assign service request to a service provider
     */
    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isLandlord()) {
            abort(403, 'Only admins and landlords can assign service requests.');
        }

        $request->validate([
            'service_provider_id' => 'required|exists:users,id' // Use service_provider_id
        ]);

        $serviceProvider = User::findOrFail($request->service_provider_id);

        if (!$serviceProvider->isServiceProvider()) {
            return redirect()->back()->with('error', 'Selected user is not a service provider.');
        }

        $serviceRequest->update([
            'service_provider_id' => $serviceProvider->id, // Use service_provider_id
            'status' => ServiceRequest::STATUS_ASSIGNED
        ]);

        // Create conversation between student and service provider
        $conversation = $this->findOrCreateConversation($serviceRequest->student_id, $serviceProvider->id);
        $conversation->update(['service_request_id' => $serviceRequest->id]);

        // Send assignment message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $serviceProvider->id,
            'message' => "Service request #{$serviceRequest->id} has been assigned to you. \n\nService Type: {$serviceRequest->service_type}\nDescription: {$serviceRequest->description}\nUrgency: {$serviceRequest->urgency_level}",
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Notify both parties
        NotificationService::createNotification(
            $serviceProvider->id,
            'New Service Request Assignment',
            "You have been assigned to service request #{$serviceRequest->id}",
            'service_request',
            route('service-requests.show', $serviceRequest->id)
        );

        NotificationService::createNotification(
            $serviceRequest->student_id,
            'Service Request Assigned',
            "Your service request has been assigned to a provider",
            'service_request',
            route('service-requests.show', $serviceRequest->id)
        );

        return redirect()->back()->with('success', 'Service request assigned successfully!');
    }

    /**
     * Update service request status
     */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$this->canModifyServiceRequest($user, $serviceRequest)) {
            abort(403, 'Unauthorized to update service request status.');
        }

        $request->validate([
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled'
        ]);

        $oldStatus = $serviceRequest->status;
        $serviceRequest->update(['status' => $request->status]);

        // Notify status change
        $this->notifyStatusChange($serviceRequest, $request->status, $oldStatus);

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    /**
     * Send message in service request conversation
     */
    public function sendMessage(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$serviceRequest->canUserAccess($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        // Determine receiver based on user type
        $receiverId = $this->getMessageReceiver($user, $serviceRequest);

        // Find or create conversation
        $conversation = $this->findOrCreateConversation($user->id, $receiverId);
        $conversation->update(['service_request_id' => $serviceRequest->id]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Notify receiver
        NotificationService::createNotification(
            $receiverId,
            'New Message - Service Request',
            "New message in service request #{$serviceRequest->id}",
            'message',
            route('service-requests.show', $serviceRequest->id)
        );

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    /**
     * Get service request conversation
     */
    public function getConversation(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$serviceRequest->canUserAccess($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation = $serviceRequest->conversation;
        $messages = $conversation ? $conversation->messages()->with('sender')->get() : collect();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'service_request' => $serviceRequest
        ]);
    }

    /**
     * Chat interface for service request
     */
    public function chat(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$serviceRequest->canUserAccess($user)) {
            abort(403, 'Unauthorized access to service request chat.');
        }

        $conversation = $serviceRequest->conversation;
        $messages = $conversation ? $conversation->messages()->with('sender')->get() : collect();

        return view('service-requests.chat', compact('serviceRequest', 'messages', 'conversation'));
    }

    /**
     * Add internal note to service request (for admins/landlords/service providers)
     */
    public function addNote(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isLandlord() && !$user->isServiceProvider()) {
            abort(403, 'Unauthorized to add notes.');
        }

        $request->validate([
            'note' => 'required|string|max:1000'
        ]);

        $notes = $serviceRequest->notes ?? [];
        $notes[] = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'note' => $request->note,
            'timestamp' => now()->toDateTimeString()
        ];

        $serviceRequest->update(['notes' => $notes]);

        return redirect()->back()->with('success', 'Note added successfully!');
    }

    /**
     * Rate completed service request (for students)
     */
    public function rate(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        if (!$user->isStudent() || $serviceRequest->student_id != $user->id) {
            abort(403, 'Unauthorized to rate this service request.');
        }

        if ($serviceRequest->status !== ServiceRequest::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Can only rate completed service requests.');
        }

        $request->validate([
            'student_rating' => 'required|integer|min:1|max:5', // Use student_rating
            'student_review' => 'nullable|string|max:1000' // Use student_review
        ]);

        $serviceRequest->update([
            'student_rating' => $request->student_rating, // Use student_rating
            'student_review' => $request->student_review, // Use student_review
            'rated_at' => now()
        ]);

        // Update service provider rating if assigned
        if ($serviceRequest->service_provider_id) {
            $this->updateServiceProviderRating($serviceRequest->service_provider_id);
        }

        return redirect()->back()->with('success', 'Thank you for your rating!');
    }

    /**
     * Check if user can access service request
     */
    private function canAccessServiceRequest(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent() && $serviceRequest->student_id === $user->id) {
            return true;
        }

        if ($user->isLandlord()) {
            return $serviceRequest->student->bookings()
                ->whereHas('hostel', function($query) use ($user) {
                    $query->where('landlord_id', $user->id);
                })
                ->exists();
        }

        if ($user->isServiceProvider() && $serviceRequest->service_provider_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can modify service request
     */
    private function canModifyServiceRequest(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent() && $serviceRequest->student_id === $user->id) {
            return in_array($serviceRequest->status, [ServiceRequest::STATUS_PENDING, ServiceRequest::STATUS_ASSIGNED]);
        }

        if ($user->isLandlord()) {
            return $this->canAccessServiceRequest($user, $serviceRequest);
        }

        if ($user->isServiceProvider() && $serviceRequest->service_provider_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Get appropriate message receiver
     */
    private function getMessageReceiver(User $user, ServiceRequest $serviceRequest): int
    {
        if ($user->isStudent()) {
            return $serviceRequest->service_provider_id ?:
                   User::where('user_type', 'admin')->first()->id;
        }

        if ($user->isServiceProvider()) {
            return $serviceRequest->student_id;
        }

        if ($user->isLandlord() || $user->isAdmin()) {
            return $serviceRequest->service_provider_id ?: $serviceRequest->student_id;
        }

        return $serviceRequest->student_id;
    }

    /**
     * Find or create conversation between two users
     */
    private function findOrCreateConversation($user1Id, $user2Id): Conversation
    {
        return Conversation::firstOrCreate([
            'user1_id' => min($user1Id, $user2Id),
            'user2_id' => max($user1Id, $user2Id)
        ], [
            'conversation_type' => 'service_request',
            'last_message_at' => now()
        ]);
    }

    /**
     * Notify relevant parties when service request is created
     */
    private function notifyServiceRequestCreation(ServiceRequest $serviceRequest): void
    {
        // Notify admins
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            NotificationService::createNotification(
                $admin->id,
                'New Service Request',
                "New {$serviceRequest->service_type} service request created",
                'service_request',
                route('service-requests.show', $serviceRequest->id)
            );
        }

        // Notify relevant landlords
        $landlords = User::where('user_type', 'landlord')
            ->whereHas('hostels.bookings', function($query) use ($serviceRequest) {
                $query->where('user_id', $serviceRequest->student_id);
            })
            ->get();

        foreach ($landlords as $landlord) {
            NotificationService::createNotification(
                $landlord->id,
                'New Tenant Service Request',
                "Your tenant has created a service request",
                'service_request',
                route('service-requests.show', $serviceRequest->id)
            );
        }
    }

    /**
     * Notify assignment changes
     */
    private function notifyAssignment(ServiceRequest $serviceRequest, $newAssigneeId): void
    {
        if ($newAssigneeId) {
            NotificationService::createNotification(
                $newAssigneeId,
                'Service Request Assigned',
                "You have been assigned to service request #{$serviceRequest->id}",
                'service_request',
                route('service-requests.show', $serviceRequest->id)
            );
        }

        // Notify student about assignment
        NotificationService::createNotification(
            $serviceRequest->student_id,
            'Service Request Update',
            "Your service request has been assigned to a provider",
            'service_request',
            route('service-requests.show', $serviceRequest->id)
        );
    }

    /**
     * Notify status changes
     */
    private function notifyStatusChange(ServiceRequest $serviceRequest, $newStatus, $oldStatus = null): void
    {
        $statusMessages = [
            'assigned' => 'has been assigned to a service provider',
            'in_progress' => 'is now in progress',
            'completed' => 'has been completed',
            'cancelled' => 'has been cancelled'
        ];

        if (isset($statusMessages[$newStatus])) {
            $message = "Your service request {$statusMessages[$newStatus]}";

            NotificationService::createNotification(
                $serviceRequest->student_id,
                'Service Request Status Update',
                $message,
                'service_request',
                route('service-requests.show', $serviceRequest->id)
            );
        }
    }

    /**
     * Update service provider rating
     */
    private function updateServiceProviderRating($serviceProviderId): void
    {
        $serviceProvider = User::find($serviceProviderId);
        if (!$serviceProvider || !$serviceProvider->isServiceProvider()) {
            return;
        }

        $completedRequests = ServiceRequest::where('service_provider_id', $serviceProviderId)
            ->whereNotNull('student_rating')
            ->get();

        if ($completedRequests->count() > 0) {
            $averageRating = $completedRequests->avg('student_rating');
            $serviceProvider->serviceProviderDetail->update([
                'rating' => round($averageRating, 1),
                'total_ratings' => $completedRequests->count()
            ]);
        }
    }
}
