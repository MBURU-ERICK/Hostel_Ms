<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class ServiceProviderController extends Controller
{
public function dashboard()
{
    $serviceProvider = Auth::user()->serviceProvider;

    if (!$serviceProvider) {
        return redirect()->route('service-provider.setup');
    }

    $stats = [
        'total_requests' => ServiceRequest::where('service_provider_id', $serviceProvider->id)->count(),
        'pending_requests' => ServiceRequest::where('service_provider_id', $serviceProvider->id)
            ->where('status', ServiceRequest::STATUS_PENDING)->count(),
        'active_jobs' => ServiceRequest::where('service_provider_id', $serviceProvider->id)
            ->whereIn('status', [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_IN_PROGRESS])->count(),
        'completed_jobs' => ServiceRequest::where('service_provider_id', $serviceProvider->id)
            ->where('status', ServiceRequest::STATUS_COMPLETED)->count(),
        'total_earnings' => ServiceRequest::where('service_provider_id', $serviceProvider->id)
            ->where('status', ServiceRequest::STATUS_COMPLETED)->sum('cost'),
        'average_rating' => ServiceRequest::where('service_provider_id', $serviceProvider->id)
            ->whereNotNull('student_rating')->avg('student_rating')
    ];

    $recentRequests = ServiceRequest::with(['student', 'hostel'])
        ->where('service_provider_id', $serviceProvider->id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $upcomingJobs = ServiceRequest::with(['student', 'hostel'])
        ->where('service_provider_id', $serviceProvider->id)
        ->whereIn('status', [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_IN_PROGRESS])
        ->where('scheduled_date', '>=', now())
        ->orderBy('scheduled_date')
        ->limit(5)
        ->get();

    // Get recent messages
    $recentMessages = Message::with('sender')
        ->where('receiver_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $unreadCount = Message::where('receiver_id', Auth::id())
        ->where('is_read', false)
        ->count();

    return view('service-provider.dashboard', compact(
        'stats',
        'recentRequests',
        'upcomingJobs',
        'serviceProvider',
        'recentMessages',
        'unreadCount'
    ));
}

    public function setup()
    {
        return view('service-provider.setup');
    }

    public function storeSetup(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'service_type' => 'required|string',
            'description' => 'required|string',
            'license_number' => 'nullable|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'hourly_rate' => 'required|numeric|min:0'
        ]);

        ServiceProvider::create([
            'user_id' => Auth::id(),
            'company_name' => $request->company_name,
            'service_type' => $request->service_type,
            'description' => $request->description,
            'license_number' => $request->license_number,
            'experience_years' => $request->experience_years,
            'hourly_rate' => $request->hourly_rate,
            'is_verified' => false,
            'rating' => 0,
            'total_jobs_completed' => 0
        ]);

        return redirect()->route('service-provider.dashboard')
            ->with('success', 'Service provider profile created successfully!');
    }

    public function serviceRequests()
    {
        $serviceProvider = Auth::user()->serviceProvider;
        $requests = ServiceRequest::with(['student', 'hostel'])
            ->where('service_provider_id', $serviceProvider->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('service-provider.requests', compact('requests'));
    }

    public function acceptRequest($id)
    {
        $request = ServiceRequest::findOrFail($id);

        if ($request->service_provider_id !== Auth::user()->serviceProvider->id) {
            abort(403);
        }

        $request->update(['status' => ServiceRequest::STATUS_ACCEPTED]);

        return back()->with('success', 'Service request accepted successfully!');
    }

    public function startJob($id)
    {
        $request = ServiceRequest::findOrFail($id);

        if ($request->service_provider_id !== Auth::user()->serviceProvider->id) {
            abort(403);
        }

        $request->update(['status' => ServiceRequest::STATUS_IN_PROGRESS]);

        return back()->with('success', 'Job marked as in progress!');
    }

    public function completeJob($id)
    {
        $request = ServiceRequest::findOrFail($id);

        if ($request->service_provider_id !== Auth::user()->serviceProvider->id) {
            abort(403);
        }

        $request->update([
            'status' => ServiceRequest::STATUS_COMPLETED,
            'completed_date' => now()
        ]);

        return back()->with('success', 'Job completed successfully!');
    }

  // app/Http/Controllers/ServiceProviderController.php
public function profile()
{
    $user = auth()->user();
    $serviceProvider = $user->serviceProvider;

    // If service provider doesn't exist, you might want to create one
    if (!$serviceProvider) {
        // Option 1: Redirect to create profile page
        // return redirect()->route('service-provider.create-profile');

        // Option 2: Create a basic service provider record
        $serviceProvider = ServiceProvider::create([
            'user_id' => $user->id,
            'business_name' => $user->name,
            'is_verified' => false,
        ]);
    }

    return view('service-provider.profile', [
        'user' => $user,
        'serviceProvider' => $serviceProvider
    ]);
}

// app/Http/Controllers/ServiceProviderController.php
public function updateProfile(Request $request)
{
    $user = auth()->user();
    $serviceProvider = $user->serviceProvider;

    // Validate user fields
    $userValidated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
    ]);

    // Update user
    $user->update([
        'name' => $userValidated['name'],
        'phone' => $userValidated['phone'],
    ]);

    // Validate service provider fields based on your model's fillable array
    $providerValidated = $request->validate([
        'company_name' => 'required|string|max:255',
        'service_type' => 'required|string',
        'description' => 'required|string',
        'license_number' => 'required|string|max:255',
        'experience_years' => 'required|integer|min:0|max:50',
        'hourly_rate' => 'required|numeric|min:0',
        'coverage_areas' => 'nullable|string',
        'response_time' => 'required|integer|min:1|max:48',
    ]);

    // Handle coverage_areas - convert from comma-separated string to array
    $coverageAreas = [];
    if (!empty($providerValidated['coverage_areas'])) {
        $coverageAreas = array_map('trim', explode(',', $providerValidated['coverage_areas']));
    }

    // Update service provider
    $serviceProvider->update([
        'company_name' => $providerValidated['company_name'],
        'service_type' => $providerValidated['service_type'],
        'description' => $providerValidated['description'],
        'license_number' => $providerValidated['license_number'],
        'experience_years' => $providerValidated['experience_years'],
        'hourly_rate' => $providerValidated['hourly_rate'],
        'coverage_areas' => $coverageAreas,
        'response_time' => $providerValidated['response_time'],
        // Note: is_verified, is_available, rating, etc. are not updated here
        // as they are managed by admin/system
    ]);

    return redirect()->route('service-provider.profile')
        ->with('success', 'Profile updated successfully!');
}
    // Add these methods to your existing ServiceProviderController

public function earnings()
{
    $serviceProvider = Auth::user()->serviceProvider;

    $earnings = ServiceRequest::where('service_provider_id', $serviceProvider->id)
        ->where('status', ServiceRequest::STATUS_COMPLETED)
        ->selectRaw('YEAR(completed_date) as year, MONTH(completed_date) as month, SUM(cost) as total')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    $totalEarnings = ServiceRequest::where('service_provider_id', $serviceProvider->id)
        ->where('status', ServiceRequest::STATUS_COMPLETED)
        ->sum('cost');

    $recentEarnings = ServiceRequest::with(['student', 'hostel'])
        ->where('service_provider_id', $serviceProvider->id)
        ->where('status', ServiceRequest::STATUS_COMPLETED)
        ->orderBy('completed_date', 'desc')
        ->limit(10)
        ->get();

    return view('service-provider.earnings', compact('earnings', 'totalEarnings', 'recentEarnings', 'serviceProvider'));
}

public function reviews()
{
    $serviceProvider = Auth::user()->serviceProvider;

    $reviews = ServiceRequest::with(['student', 'hostel'])
        ->where('service_provider_id', $serviceProvider->id)
        ->whereNotNull('student_rating')
        ->orderBy('completed_date', 'desc')
        ->paginate(10);

    $averageRating = ServiceRequest::where('service_provider_id', $serviceProvider->id)
        ->whereNotNull('student_rating')
        ->avg('student_rating');

    $ratingDistribution = ServiceRequest::where('service_provider_id', $serviceProvider->id)
        ->whereNotNull('student_rating')
        ->selectRaw('student_rating, COUNT(*) as count')
        ->groupBy('student_rating')
        ->orderBy('student_rating', 'desc')
        ->get();

    return view('service-provider.reviews', compact('reviews', 'averageRating', 'ratingDistribution', 'serviceProvider'));
}
public function messages()
{
    $serviceProvider = Auth::user()->serviceProvider;

    // Get unique conversations
    $conversations = Message::where('receiver_id', Auth::id())
        ->orWhere('sender_id', Auth::id())
        ->with(['sender', 'receiver'])
        ->select('sender_id', 'receiver_id')
        ->selectRaw('MAX(created_at) as last_message_time')
        ->groupBy('sender_id', 'receiver_id')
        ->orderBy('last_message_time', 'desc')
        ->get()
        ->map(function($message) {
            $otherUserId = $message->sender_id == Auth::id() ? $message->receiver_id : $message->sender_id;
            $otherUser = $message->sender_id == Auth::id() ? $message->receiver : $message->sender;

            $lastMessage = Message::where(function($query) use ($otherUserId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $otherUserId);
            })->orWhere(function($query) use ($otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('receiver_id', Auth::id());
            })->latest()->first();

            $unreadCount = Message::where('sender_id', $otherUserId)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return (object)[
                'id' => $otherUserId,
                'other_user' => $otherUser,
                'last_message' => $lastMessage->message ?? 'No messages yet',
                'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                'unread_count' => $unreadCount,
                'messages' => Message::where(function($query) use ($otherUserId) {
                    $query->where('sender_id', Auth::id())
                          ->where('receiver_id', $otherUserId);
                })->orWhere(function($query) use ($otherUserId) {
                    $query->where('sender_id', $otherUserId)
                          ->where('receiver_id', Auth::id());
                })->orderBy('created_at', 'asc')->get()
            ];
        });

    $selectedConversation = null;
    $selectedConversationId = request('conversation_id');

    if ($selectedConversationId) {
        $selectedConversation = $conversations->firstWhere('id', $selectedConversationId);

        // Mark messages as read
        if ($selectedConversation) {
            Message::where('sender_id', $selectedConversationId)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    // Get clients (students and landlords you've interacted with)
    $clients = User::whereIn('id', function($query) {
        $query->select('sender_id')
              ->from('messages')
              ->where('receiver_id', Auth::id())
              ->union(
                  DB::table('messages')->select('receiver_id')
                  ->where('sender_id', Auth::id())
              );
    })->where('id', '!=', Auth::id())->get();

    $unreadCount = Message::where('receiver_id', Auth::id())
        ->where('is_read', false)
        ->count();

    return view('service-provider.messages', compact(
        'conversations',
        'selectedConversation',
        'clients',
        'unreadCount',
        'serviceProvider'
    ));
}

public function sendMessage(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'message' => 'required|string|max:1000'
    ]);

    $message = Message::create([
        'sender_id' => Auth::id(),
        'receiver_id' => $request->receiver_id,
        'message' => $request->message,
        'is_read' => false
    ]);

    // Create notification for the receiver
    $senderName = Auth::user()->name;

    NotificationService::createNotification(
        $request->receiver_id,
        'New Message',
        "You have a new message from {$senderName}",
        'message',
        route('messages.index', ['conversation_id' => Auth::id()])
    );

    return response()->json([
        'success' => true,
        'message' => $message
    ]);
}

}
