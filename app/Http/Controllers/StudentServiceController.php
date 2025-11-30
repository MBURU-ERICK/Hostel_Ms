<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\ServiceProvider;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentServiceController extends Controller
{
    public function index()
    {
        $services = ServiceProvider::where('is_verified', true)->get();
        $myRequests = ServiceRequest::with('serviceProvider')
            ->where('student_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.services.index', compact('services', 'myRequests'));
    }

    public function create()
    {
        $serviceTypes = (new ServiceProvider())->serviceTypes();
        $hostels = Hostel::all(); // Or get hostels where student has bookings

        return view('student.services.create', compact('serviceTypes', 'hostels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'hostel_id' => 'required|exists:hostels,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'urgency_level' => 'required|string',
            'address' => 'required|string',
            'room_number' => 'required|string'
        ]);

        ServiceRequest::create([
            'student_id' => Auth::id(),
            'hostel_id' => $request->hostel_id,
            'service_type' => $request->service_type,
            'title' => $request->title,
            'description' => $request->description,
            'urgency_level' => $request->urgency_level,
            'status' => ServiceRequest::STATUS_PENDING,
            'address' => $request->address,
            'room_number' => $request->room_number
        ]);

        return redirect()->route('student.services.index')
            ->with('success', 'Service request submitted successfully! A service provider will contact you soon.');
    }

    public function rateService(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        if ($serviceRequest->student_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $serviceRequest->update([
            'student_rating' => $request->rating,
            'student_review' => $request->review
        ]);

        // Update service provider's average rating
        $serviceProvider = $serviceRequest->serviceProvider;
        $averageRating = ServiceRequest::where('service_provider_id', $serviceProvider->id)
            ->whereNotNull('student_rating')
            ->avg('student_rating');

        $serviceProvider->update(['rating' => $averageRating]);

        return back()->with('success', 'Thank you for your feedback!');
    }
}
