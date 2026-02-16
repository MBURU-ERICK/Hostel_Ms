<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hostel;
use App\Models\Booking;
use App\Services\NotificationService;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $bookings = [];
        $notifications = [];

        return view('student.dashboard', compact('user', 'bookings', 'notifications'));
    }

    public function searchHostels(Request $request)
{
    $query = Hostel::approved()->available();

    // Basic search
    if ($request->filled('location')) {
        $query->where(function($q) use ($request) {
            $q->where('location', 'like', '%' . $request->location . '%')
              ->orWhere('address', 'like', '%' . $request->location . '%');
        });
    }

    // Price range
    if ($request->filled('min_price')) {
        $query->where('rent_per_month', '>=', $request->min_price);
    }
    if ($request->filled('max_price')) {
        $query->where('rent_per_month', '<=', $request->max_price);
    }

    // Room type (you'll need to add room_type to your hostels table)
    if ($request->filled('room_type')) {
        $query->where('room_type', $request->room_type);
    }

    // Amenities filter
    if ($request->filled('amenities')) {
        $amenities = $request->amenities;
        $query->where(function($q) use ($amenities) {
            foreach ($amenities as $amenity) {
                $q->orWhereJsonContains('amenities', $amenity);
            }
        });
    }

    // Minimum rating
    if ($request->filled('min_rating')) {
        $minRating = $request->min_rating;
        $query->whereHas('reviews', function($q) use ($minRating) {
            $q->select('hostel_id')
              ->selectRaw('AVG(rating) as avg_rating')
              ->groupBy('hostel_id')
              ->having('avg_rating', '>=', $minRating);
        });
    }

    // Deposit amount
    if ($request->filled('max_deposit')) {
        $query->where('deposit_amount', '<=', $request->max_deposit);
    }

    // Minimum rooms available
    if ($request->filled('min_rooms')) {
        $query->where('rooms_available', '>=', $request->min_rooms);
    }

    // Instant booking (you can add an instant_booking field to hostels)
    if ($request->filled('instant_booking')) {
        $query->where('instant_booking', true);
    }

    // Sorting
    switch ($request->get('sort_by', 'newest')) {
        case 'price_low':
            $query->orderBy('rent_per_month', 'asc');
            break;
        case 'price_high':
            $query->orderBy('rent_per_month', 'desc');
            break;
        case 'rating':
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
            break;
        case 'name':
            $query->orderBy('name', 'asc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
    }

    $perPage = $request->get('per_page', 12);
    $hostels = $query->with(['landlord', 'reviews'])->paginate($perPage);

    return view('student.search-hostels', compact('hostels'));
}

public function viewHostel($id)
{
    $hostel = Hostel::with('landlord')->approved()->available()->findOrFail($id);

    // Get similar hostels for recommendations
    $similarHostels = Hostel::with('landlord')
        ->approved()
        ->available()
        ->where('location', $hostel->location)
        ->where('id', '!=', $hostel->id)
        ->take(3)
        ->get();

    return view('student.view-hostel', compact('hostel', 'similarHostels'));
}
    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'admission_number' => 'required|string|max:255|unique:student_profiles,admission_number,' . ($user->studentProfile ? $user->studentProfile->id : 'NULL') . ',id,user_id,' . $user->id,
            'id_number' => 'required|string|max:255|unique:student_profiles,id_number,' . ($user->studentProfile ? $user->studentProfile->id : 'NULL') . ',id,user_id,' . $user->id,
            'gender' => 'required|in:male,female,other',
            'institution_name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'year_of_study' => 'required|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
        ]);

        // Update user basic info
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Update or create student profile
        $profileData = [
            'admission_number' => $request->admission_number,
            'id_number' => $request->id_number,
            'gender' => $request->gender,
            'institution_name' => $request->institution_name,
            'course' => $request->course,
            'year_of_study' => $request->year_of_study,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'address' => $request->address,
        ];

        if ($user->studentProfile) {
            $user->studentProfile->update($profileData);
        } else {
            $user->studentProfile()->create($profileData);
        }

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
    // Add these methods to the StudentController

public function bookHostel(Request $request, $hostelId)
{
    $hostel = Hostel::approved()->available()->findOrFail($hostelId);
    $user = Auth::user();

    // Check if student profile is complete
    if (!$user->studentProfile) {
        return redirect()->route('student.profile')
            ->with('error', 'Please complete your student profile before making a booking.');
    }

    // Check if there are available rooms
    if ($hostel->rooms_available <= 0) {
        return redirect()->back()->with('error', 'Sorry, this hostel is fully booked.');
    }

    $request->validate([
        'duration_months' => 'required|integer|min:1|max:12',
        'check_in_date' => 'required|date|after:today',
        'special_requests' => 'nullable|string|max:500',
    ]);

    // Calculate total amount
    $totalAmount = ($hostel->rent_per_month * $request->duration_months) + $hostel->deposit_amount;

    // Create booking - CHANGE 'user_id' to 'student_id'
    $booking = Booking::create([
        'user_id' => $user->id,  // Changed from 'user_id' to 'student_id'
        'hostel_id' => $hostel->id,
        'check_in_date' => $request->check_in_date,
        'duration_months' => $request->duration_months,
        'total_amount' => $totalAmount,
        'amount_paid' => 0,
        'special_requests' => $request->special_requests,
        'booking_status' => 'pending',
        'payment_status' => 'pending',
    ]);

    // Update hostel availability
    $hostel->decrement('rooms_available');

    // Send notifications
    NotificationService::notifyBookingCreated($booking);
    NotificationService::notifyLandlordNewBooking($booking);

    return redirect()->route('student.booking-confirmation', $booking->id)
        ->with('success', 'Booking request submitted successfully!');
}

public function bookingConfirmation($bookingId)
{
    $booking = Booking::with(['hostel', 'user'])->where('user_id', Auth::id())->findOrFail($bookingId);

    return view('student.booking-confirmation', compact('booking'));
}

public function myBookings()
{
    $bookings = Booking::with(['hostel', 'messages'])
        ->where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('student.my-bookings', compact('bookings'));
}

public function bookingDetails($id)
{
    $booking = Booking::with(['hostel', 'payments', 'messages'])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    return view('student.booking-details', compact('booking'));
}

public function cancelBooking(Request $request, $id)
{
    $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

    if ($booking->booking_status === 'confirmed' && $booking->payment_status === 'paid') {
        return response()->json([
            'success' => false,
            'message' => 'Cannot cancel a confirmed and paid booking. Please contact support.'
        ], 400);
    }

    $booking->update([
        'booking_status' => 'cancelled',
        'cancelled_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Booking cancelled successfully.'
    ]);
}
public function notifications()
    {
        $notifications = NotificationService::getAllNotifications(Auth::id());
        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        return view('student.notifications', compact('notifications', 'unreadCount'));
    }
    public function messages()
    {
        $user = Auth::user();

        // Get all bookings where the student has messages
        $bookingsWithMessages = Booking::where('user_id', $user->id)
            ->whereHas('messages')
            ->with(['hostel', 'messages' => function($query) {
                $query->latest();
            }])
            ->latest()
            ->get();

        // Use the correct view that expects a collection of bookings
        return view('student.messages', compact('bookingsWithMessages'));
    }

    /**
     * Show messages for a specific booking
     */
    public function bookingMessages($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['hostel', 'messages.user'])
            ->findOrFail($bookingId);

        // This should use the view that expects a single $booking
        return view('student.messages.index', compact('booking'));
    }


}
