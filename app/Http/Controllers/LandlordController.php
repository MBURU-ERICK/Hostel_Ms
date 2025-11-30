<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LandlordController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $hostels = Hostel::where('landlord_id', $user->id)->get();

        // Get hostel IDs for queries
        $hostelIds = $hostels->pluck('id');

        $stats = [
            'total_hostels' => $hostels->count(),
            'total_bookings' => $hostelIds->isNotEmpty()
                ? Booking::whereIn('hostel_id', $hostelIds)->count()
                : 0,
            'pending_bookings' => $hostelIds->isNotEmpty()
                ? Booking::whereIn('hostel_id', $hostelIds)
                    ->where('booking_status', 'pending')
                    ->count()
                : 0,
            'total_earnings' => $hostelIds->isNotEmpty()
                ? Booking::whereIn('hostel_id', $hostelIds)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount')
                : 0,
        ];

        $recentBookings = $hostelIds->isNotEmpty()
            ? Booking::with(['hostel', 'student'])
                ->whereIn('hostel_id', $hostelIds)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
            : collect();

        $recentMessages = $hostelIds->isNotEmpty()
            ? Message::with(['booking.hostel', 'sender'])
                ->whereHas('booking', function($query) use ($hostelIds) {
                    $query->whereIn('hostel_id', $hostelIds);
                })
                ->where('receiver_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
            : collect();

        return view('landlord.dashboard', compact('stats', 'recentBookings', 'recentMessages', 'hostels'));
    }

    public function hostels()
    {
        $hostels = Hostel::withCount(['bookings', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('landlord_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('landlord.hostels.index', compact('hostels'));
    }

    public function createHostel()
    {
        return view('landlord.hostels.create');
    }

    public function storeHostel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'rent_per_month' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'rooms_available' => 'required|integer|min:0',
            'amenities' => 'required|array|min:1',
            'amenities.*' => 'string|max:255',
            'rules' => 'nullable|string|max:1000',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('hostel-images', 'public');
                $imagePaths[] = $path;
            }
        }

        $hostel = Hostel::create([
            'landlord_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'address' => $validated['address'],
            'rent_per_month' => $validated['rent_per_month'],
            'deposit_amount' => $validated['deposit_amount'],
            'total_rooms' => $validated['total_rooms'],
            'rooms_available' => $validated['rooms_available'],
            'amenities' => $validated['amenities'],
            'rules' => $validated['rules'] ?? null,
            'contact_phone' => $validated['contact_phone'],
            'contact_email' => $validated['contact_email'],
            'images' => $imagePaths,
            'is_approved' => false, // Needs admin approval
            'is_available' => true,
        ]);

        return redirect()->route('landlord.hostels')
            ->with('success', 'Hostel created successfully! It will be visible after admin approval.');
    }

    public function editHostel($id)
    {
        $hostel = Hostel::where('landlord_id', Auth::id())->findOrFail($id);
        return view('landlord.hostels.edit', compact('hostel'));
    }
public function toggleAvailability($id)
{
    $hostel = Hostel::where('landlord_id', Auth::id())->findOrFail($id);

    // Store the current value before updating
    $currentAvailability = $hostel->is_available;

    $hostel->update([
        'is_available' => !$currentAvailability,
        'is_approved' => !$currentAvailability ? false : $hostel->is_approved, // Require re-approval only when making available
    ]);

    $message = !$currentAvailability
        ? 'Hostel is now available for bookings!'
        : 'Hostel is now unavailable for bookings.';

    return redirect()->back()->with('success', $message);
}


    public function updateHostel(Request $request, $id)
    {
        $hostel = Hostel::where('landlord_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'rent_per_month' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:1',
            'rooms_available' => 'required|integer|min:0',
            'amenities' => 'required|array|min:1',
            'amenities.*' => 'string|max:255',
            'rules' => 'nullable|string|max:1000',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image uploads
        $imagePaths = $hostel->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old images if needed
            foreach ($imagePaths as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('hostel-images', 'public');
                $imagePaths[] = $path;
            }
        }

        $hostel->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'address' => $validated['address'],
            'rent_per_month' => $validated['rent_per_month'],
            'deposit_amount' => $validated['deposit_amount'],
            'total_rooms' => $validated['total_rooms'],
            'rooms_available' => $validated['rooms_available'],
            'amenities' => $validated['amenities'],
            'rules' => $validated['rules'] ?? null,
            'contact_phone' => $validated['contact_phone'],
            'contact_email' => $validated['contact_email'],
            'images' => $imagePaths,
            'is_approved' => false, // Require re-approval after edits
        ]);

        return redirect()->route('landlord.hostels')
            ->with('success', 'Hostel updated successfully! It will be visible after admin approval.');
    }
    public function deleteHostel($id)
{
    $hostel = Hostel::where('landlord_id', Auth::id())->findOrFail($id);
    return view('landlord.hostels.delete', compact('hostel'));
}

public function destroyHostel($id)
{
    $hostel = Hostel::where('landlord_id', Auth::id())->findOrFail($id);

    // Validate confirmation
    request()->validate([
        'confirmation' => 'required|in:DELETE'
    ]);

    // Delete associated images
    if ($hostel->images) {
        foreach ($hostel->images as $image) {
            Storage::disk('public')->delete($image);
        }
    }

    // Delete the hostel
    $hostel->delete();

    return redirect()->route('landlord.hostels')
        ->with('success', 'Hostel deleted successfully!');
}

    public function bookings()
    {
        $bookings = Booking::with(['hostel', 'student'])
            ->whereHas('hostel', function($query) {
                $query->where('landlord_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('landlord.bookings.index', compact('bookings'));
    }

public function updateBookingStatus(Request $request, $id)
{
    $booking = Booking::with('hostel')
        ->whereHas('hostel', function($query) {
            $query->where('landlord_id', Auth::id());
        })
        ->findOrFail($id);

    $request->validate([
        'status' => 'required|in:confirmed,cancelled'
    ]);

    $oldStatus = $booking->booking_status;
    $booking->update(['booking_status' => $request->status]);

    // Send notification to student
    if ($request->status === 'confirmed') {
        NotificationService::notifyBookingConfirmed($booking);

        // Update hostel availability
        $booking->hostel->decrement('rooms_available');
    } elseif ($request->status === 'cancelled') {
        NotificationService::notifyBookingCancelled($booking);
    }

    return redirect()->back()->with('success', 'Booking status updated successfully!');
}

    public function messages()
    {
        $landlordId = Auth::id();

        // Get landlord's hostels
        $hostelIds = Hostel::where('landlord_id', $landlordId)->pluck('id');

        // Simple stats calculation
        $stats = [
            'total_messages' => 0,
            'unread_messages' => 0,
            'active_conversations' => 0,
        ];

        $conversations = collect();
        $selectedConversation = null;

        if ($hostelIds->isNotEmpty()) {
            $stats = [
                'total_messages' => Message::whereHas('booking', function($query) use ($hostelIds) {
                        $query->whereIn('hostel_id', $hostelIds);
                    })->count(),
                'unread_messages' => Message::whereHas('booking', function($query) use ($hostelIds) {
                        $query->whereIn('hostel_id', $hostelIds);
                    })
                    ->where('is_read', false)
                    ->count(),
                'active_conversations' => Booking::whereIn('hostel_id', $hostelIds)
                    ->whereHas('messages')
                    ->count(),
            ];

            // Get bookings with messages
            $conversations = Booking::whereIn('hostel_id', $hostelIds)
                ->with(['student', 'hostel', 'messages' => function($query) {
                    $query->orderBy('created_at', 'desc')->limit(1);
                }])
                ->whereHas('messages')
                ->get()
                ->map(function($booking) use ($landlordId) {
                    $lastMessage = $booking->messages->first();
                    $unreadCount = $booking->messages()
                        ->where('is_read', false)
                        ->where('sender_id', '!=', $landlordId)
                        ->count();

                    return (object) [
                        'id' => $booking->id,
                        'student' => $booking->student,
                        'hostel' => $booking->hostel,
                        'last_message' => $lastMessage ? $lastMessage->message : 'No messages',
                        'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                        'unread_count' => $unreadCount,
                        'is_selected' => false,
                    ];
                })
                ->sortByDesc('last_message_time')
                ->values();
        }

        return view('landlord.messages.index', compact(
            'stats',
            'conversations',
            'selectedConversation'
        ));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:bookings,id',
            'message' => 'required|string|max:1000',
        ]);

        $booking = Booking::with('hostel')->findOrFail($request->conversation_id);

        // Verify that the booking belongs to the landlord
        if ($booking->hostel->landlord_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        Message::create([
            'booking_id' => $booking->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function reviews()
    {
        $reviews = Review::with(['hostel', 'user'])
            ->whereHas('hostel', function($query) {
                $query->where('landlord_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('landlord.reviews.index', compact('reviews'));
    }

  public function earnings()
{
    $earnings = Booking::with(['hostel', 'student'])
        ->whereHas('hostel', function($query) {
            $query->where('landlord_id', Auth::id());
        })
        ->where('payment_status', 'paid')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $totalEarnings = Booking::whereHas('hostel', function($query) {
            $query->where('landlord_id', Auth::id());
        })
        ->where('payment_status', 'paid')
        ->sum('total_amount');

    $monthlyEarnings = Booking::whereHas('hostel', function($query) {
            $query->where('landlord_id', Auth::id());
        })
        ->where('payment_status', 'paid')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->sum('total_amount');

    return view('landlord.earnings.index', compact('earnings', 'totalEarnings', 'monthlyEarnings'));
}
    public function respondToReview(Request $request, $id)
{
    $review = Review::with('hostel')
        ->whereHas('hostel', function($query) {
            $query->where('landlord_id', Auth::id());
        })
        ->findOrFail($id);

    $request->validate([
        'response' => 'required|string|max:1000'
    ]);

    $review->update([
        'landlord_response' => $request->response,
        'responded_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Response posted successfully!');
}



}
