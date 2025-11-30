<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use App\Models\Hostel;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create($bookingId)
    {
        $booking = Booking::with('hostel')
            ->where('user_id', Auth::id())
            ->where('booking_status', 'confirmed')
            ->findOrFail($bookingId);

        // Check if user has already reviewed this booking
        $existingReview = Review::where('booking_id', $bookingId)->first();
        if ($existingReview) {
            return redirect()->route('hostel.show', $booking->hostel_id)
                ->with('error', 'You have already reviewed this booking.');
        }

        return view('reviews.create', compact('booking'));
    }

    public function store(Request $request, $bookingId)
    {
        $booking = Booking::with('hostel')
            ->where('user_id', Auth::id())
            ->where('booking_status', 'confirmed')
            ->findOrFail($bookingId);

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'hostel_id' => $booking->hostel_id,
            'booking_id' => $bookingId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true // Auto-approve for now, can be moderated later
        ]);

        // Notify landlord about new review
        NotificationService::createNotification(
            $booking->hostel->landlord_id,
            'New Review Received',
            "Your hostel {$booking->hostel->name} received a new {$request->rating}-star review",
            'review',
            route('landlord.reviews')
        );

        return redirect()->route('student.view-hostel', $booking->hostel_id)
            ->with('success', 'Thank you for your review!');
    }

    public function edit($id)
    {
        $review = Review::with('hostel')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if (!$review->canBeEdited()) {
            return redirect()->back()->with('error', 'Review can only be edited within 24 hours of posting.');
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        if (!$review->canBeEdited()) {
            return redirect()->back()->with('error', 'Review can only be edited within 24 hours of posting.');
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('student.view-hostel', $review->hostel_id)
            ->with('success', 'Review updated successfully!');
    }

    public function destroy($id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);
        $hostelId = $review->hostel_id;
        
        $review->delete();

        return redirect()->route('student.view-hostel', $hostelId)
            ->with('success', 'Review deleted successfully!');
    }

    public function hostelReviews($hostelId)
    {
        $hostel = Hostel::approved()->findOrFail($hostelId);
        $reviews = $hostel->approvedReviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reviews.hostel-reviews', compact('hostel', 'reviews'));
    }
}