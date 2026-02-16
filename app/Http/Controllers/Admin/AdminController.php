<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Booking;
use App\Models\Payment; // Add this import
use App\Models\ServiceRequest;
use App\Models\ServiceProvider;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\NotificationService;


class AdminController extends Controller
{
    /**
     * Display admin dashboard with comprehensive statistics
     */
    public function dashboard(): View
    {
        try {
            // System Statistics with error handling
            $stats = $this->getDashboardStats();

            // Recent Activities
            $recentData = $this->getRecentActivities();

            // System Analytics
            $analytics = $this->getSystemAnalytics();

            // Add payment statistics
            $paymentStats = $this->getPaymentStats();

            return view('admin.dashboard', array_merge($stats, $recentData, $analytics, $paymentStats));

        } catch (\Exception $e) {
            // Fallback dashboard with basic data
            return $this->getFallbackDashboard();
        }
    }

    /**
     * Get payment statistics for admin dashboard
     */
    private function getPaymentStats(): array
    {
        try {
            $paymentStats = [
                'total_payments' => Payment::count(),
                'successful_payments' => Payment::where('status', 'successful')->count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'failed_payments' => Payment::where('status', 'failed')->count(),
                'cancelled_payments' => Payment::where('status', 'cancelled')->count(),
                'total_revenue' => Payment::where('status', 'successful')->sum('amount') ?? 0,
                'today_revenue' => Payment::where('status', 'successful')
                    ->whereDate('completed_at', today())
                    ->sum('amount') ?? 0,
                'this_month_revenue' => Payment::where('status', 'successful')
                    ->whereMonth('completed_at', now()->month)
                    ->whereYear('completed_at', now()->year)
                    ->sum('amount') ?? 0,
                'last_month_revenue' => Payment::where('status', 'successful')
                    ->whereMonth('completed_at', now()->subMonth()->month)
                    ->whereYear('completed_at', now()->subMonth()->year)
                    ->sum('amount') ?? 0,
                'average_payment' => Payment::where('status', 'successful')->avg('amount') ?? 0,
                'recent_payments' => Payment::with(['user', 'booking.hostel'])
                    ->latest()
                    ->limit(10)
                    ->get(),
            ];

            // Format amounts
            $paymentStats['formatted_total_revenue'] = 'KSh ' . number_format($paymentStats['total_revenue'], 2);
            $paymentStats['formatted_today_revenue'] = 'KSh ' . number_format($paymentStats['today_revenue'], 2);
            $paymentStats['formatted_month_revenue'] = 'KSh ' . number_format($paymentStats['this_month_revenue'], 2);
            $paymentStats['formatted_average_payment'] = 'KSh ' . number_format($paymentStats['average_payment'], 2);

            return ['paymentStats' => $paymentStats];

        } catch (\Exception $e) {
            \Log::error('Error getting payment stats: ' . $e->getMessage());
            
            return [
                'paymentStats' => [
                    'total_payments' => 0,
                    'successful_payments' => 0,
                    'pending_payments' => 0,
                    'failed_payments' => 0,
                    'cancelled_payments' => 0,
                    'total_revenue' => 0,
                    'today_revenue' => 0,
                    'this_month_revenue' => 0,
                    'last_month_revenue' => 0,
                    'average_payment' => 0,
                    'formatted_total_revenue' => 'KSh 0.00',
                    'formatted_today_revenue' => 'KSh 0.00',
                    'formatted_month_revenue' => 'KSh 0.00',
                    'formatted_average_payment' => 'KSh 0.00',
                    'recent_payments' => collect(),
                ]
            ];
        }
    }
    /**
 * Get booking details for AJAX modal
 */
public function getBookingDetails($id)
{
    try {
        $booking = Booking::with(['user', 'hostel', 'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);

        $html = view('admin.bookings.partials.details-modal', compact('booking'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading booking details: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to load booking details'
        ], 500);
    }
}

    /**
     * Get dashboard statistics with proper error handling
     */
    private function getDashboardStats(): array
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('user_type', 'student')->count(),
            'total_landlords' => User::where('user_type', 'landlord')->count(),
            'total_service_providers' => User::where('user_type', 'service_provider')->count(),
            'total_hostels' => Hostel::count(),
            'total_service_requests' => ServiceRequest::count(),
            'pending_service_requests' => ServiceRequest::where('status', 'pending')->count(),
            'pending_approvals' => User::where('is_approved', false)->count(),
            'suspended_users' => User::where('is_active', false)->count(),
        ];

        // Safely handle booking statistics
        try {
            // Check if status column exists in bookings table
            if (Schema::hasColumn('bookings', 'status')) {
                $stats['active_bookings'] = Booking::whereIn('status', ['confirmed', 'active'])->count();
                $stats['pending_bookings'] = Booking::where('status', 'pending')->count();
                $stats['total_earnings'] = Booking::where('status', 'completed')->sum('total_amount') ?? 0;
                $stats['monthly_earnings'] = Booking::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount') ?? 0;
            } else {
                // Fallback if status column doesn't exist
                $stats['active_bookings'] = Booking::count();
                $stats['pending_bookings'] = 0;
                $stats['total_earnings'] = 0;
                $stats['monthly_earnings'] = 0;
            }
        } catch (\Exception $e) {
            // Fallback values if any booking query fails
            $stats['active_bookings'] = Booking::count();
            $stats['pending_bookings'] = 0;
            $stats['total_earnings'] = 0;
            $stats['monthly_earnings'] = 0;
        }

        return compact('stats');
    }

    /**
     * Get recent activities data
     */
    private function getRecentActivities(): array
    {
        $recentBookings = Booking::with(['user', 'hostel'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentServiceRequests = ServiceRequest::with(['student', 'serviceProvider'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Add recent payments
        $recentPayments = Payment::with(['user', 'booking.hostel'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return compact('recentBookings', 'recentServiceRequests', 'recentUsers', 'recentPayments');
    }

    /**
     * Get system analytics data
     */
    private function getSystemAnalytics(): array
    {
        // User growth (last 6 months) with error handling
        try {
            $userGrowth = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        } catch (\Exception $e) {
            $userGrowth = collect();
        }

        // Booking trends with error handling
        try {
            $bookingTrends = Booking::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        } catch (\Exception $e) {
            $bookingTrends = collect();
        }

        // Payment trends
        try {
            $paymentTrends = Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
                ->where('status', 'successful')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        } catch (\Exception $e) {
            $paymentTrends = collect();
        }

        return compact('userGrowth', 'bookingTrends', 'paymentTrends');
    }
/**
     * Display all payments with filtering
     */
    public function payments(Request $request): View
    {
        $query = Payment::with(['user', 'booking.hostel']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_revenue' => Payment::where('status', 'successful')->sum('amount') ?? 0,
            'total_payments' => Payment::count(),
            'successful_payments' => Payment::where('status', 'successful')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'today_revenue' => Payment::where('status', 'successful')
                ->whereDate('completed_at', today())
                ->sum('amount') ?? 0,
            'this_month_revenue' => Payment::where('status', 'successful')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->sum('amount') ?? 0,
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }
     /**
     * Show individual payment details
     */
    public function showPayment($id): View
    {
        $payment = Payment::with(['user', 'booking.hostel', 'booking.user'])
            ->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update the bookings method to include payment status
     */
    public function bookings(): View
    {
        $bookings = Booking::with(['user', 'hostel', 'payments' => function($query) {
                $query->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('booking_status', 'confirmed')->count(),
            'completed_bookings' => Booking::where('booking_status', 'completed')->count(),
            'cancelled_bookings' => Booking::where('booking_status', 'cancelled')->count(),
            'total_revenue' => Payment::where('status', 'successful')->sum('amount') ?? 0,
            'paid_bookings' => Booking::where('payment_status', 'paid')->count(),
            'unpaid_bookings' => Booking::where('payment_status', 'pending')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }



    /**
     * Fallback dashboard when there are database issues
     */
    private function getFallbackDashboard(): View
    {
        $fallbackStats = [
            'total_users' => 0,
            'total_students' => 0,
            'total_landlords' => 0,
            'total_service_providers' => 0,
            'total_hostels' => 0,
            'active_bookings' => 0,
            'pending_bookings' => 0,
            'total_service_requests' => 0,
            'pending_service_requests' => 0,
            'total_earnings' => 0,
            'monthly_earnings' => 0,
            'pending_approvals' => 0,
            'suspended_users' => 0,
        ];

        $emptyCollection = collect();

        return view('admin.dashboard', [
            'stats' => $fallbackStats,
            'recentBookings' => $emptyCollection,
            'recentServiceRequests' => $emptyCollection,
            'recentUsers' => $emptyCollection,
            'userGrowth' => $emptyCollection,
            'bookingTrends' => $emptyCollection,
        ])->with('warning', 'Some data may not be available due to system configuration.');
    }

    /**
     * Display all users with pagination
     */
    public function users(): View
    {
        $users = User::where('user_type', '!=', 'admin')
            ->withCount(['bookings', 'serviceRequests'])
            ->with('studentProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show individual user details
     */
    public function showUser($id): View
    {
        $user = User::with(['studentProfile', 'bookings.hostel', 'serviceRequests.serviceProvider'])
            ->withCount(['bookings', 'serviceRequests', ])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Update user information with optional approval notification
     */
    public function updateUser(Request $request, $id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'user_type' => 'required|in:student,landlord,service_provider,admin',
                'is_active' => 'boolean',
                'is_approved' => 'boolean',
            ]);

            $wasApproved = $user->is_approved;
            $isNowApproved = $request->has('is_approved');

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'user_type' => $validated['user_type'],
                'is_active' => $request->has('is_active'),
                'is_approved' => $isNowApproved,
                'approved_at' => $isNowApproved ? now() : null,
            ]);

            // If user was just approved, send notification
            if (!$wasApproved && $isNowApproved) {
                NotificationService::sendAccountApproval($user);
            }

            return redirect()->back()->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }
    /**
     * Approve a user account with email notification
     */
    public function approve($id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $user = User::findOrFail($id);

                $user->update([
                    'is_approved' => true,
                    'approved_at' => now(),
                    'is_active' => true,
                ]);

                // If it's a service provider, also mark as verified
                if ($user->user_type === 'service_provider' && $user->serviceProvider) {
                    $user->serviceProvider->update([
                        'is_verified' => true,
                        'is_available' => true
                    ]);
                }

                // Send approval email notification
                NotificationService::sendAccountApproval($user);
            });

            return redirect()->back()->with('success', 'User approved successfully and notification sent!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving user: ' . $e->getMessage());
        }
    }


    /**
     * Suspend a user account
     */
    public function suspend($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'is_active' => false,
            ]);

            return redirect()->back()->with('success', 'User suspended successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error suspending user: ' . $e->getMessage());
        }
    }

    /**
     * Activate a user account
     */
    public function activate($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'User activated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error activating user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user account
     */
    public function destroyUser($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent admin from deleting themselves
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('admin.users')->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Display all hostels
     */
public function hostels(): View
{
    $hostels = Hostel::with(['landlord', 'bookings']) // Changed 'user' to 'landlord'
        ->withCount(['bookings', 'reviews'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('admin.hostels.index', compact('hostels'));
}

    public function showHostel($id): View  // Make sure this matches your route
    {
        $hostel = Hostel::with(['landlord', 'bookings.student', 'reviews.user'])
            ->findOrFail($id);

        return view('admin.hostels.show', compact('hostel'));
    }
    /**
     * Update hostel information
     */
    public function updateHostel(Request $request, $id): RedirectResponse
    {
        try {
            $hostel = Hostel::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'price_per_month' => 'required|numeric|min:0',
                'available_rooms' => 'required|integer|min:0',
                'is_available' => 'boolean',
                'is_approved' => 'boolean',
            ]);

            $hostel->update($validated);

            return redirect()->back()->with('success', 'Hostel updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating hostel: ' . $e->getMessage());
        }
    }
/**
 * Upload images for hostel
 */
public function uploadHostelImages(Request $request, $id): RedirectResponse
{
    try {
        $hostel = Hostel::findOrFail($id);
        
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $uploadedImages = [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('hostels/' . $id, 'public');
                $uploadedImages[] = $path;
            }
        }
        
        // Merge with existing images
        $existingImages = $hostel->images ?? [];
        $hostel->images = array_merge($existingImages, $uploadedImages);
        $hostel->save();
        
        return redirect()->back()->with('success', count($uploadedImages) . ' image(s) uploaded successfully!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error uploading images: ' . $e->getMessage());
    }
}

/**
 * Delete hostel image
 */
public function deleteHostelImage(Request $request, $id): RedirectResponse
{
    try {
        $hostel = Hostel::findOrFail($id);
        
        $request->validate([
            'image_path' => 'required|string'
        ]);
        
        $imagePath = $request->image_path;
        
        // Remove from images array
        $images = $hostel->images ?? [];
        $images = array_filter($images, function($img) use ($imagePath) {
            return $img !== $imagePath;
        });
        
        $hostel->images = array_values($images);
        $hostel->save();
        
        // Delete file from storage
        \Storage::disk('public')->delete($imagePath);
        
        return redirect()->back()->with('success', 'Image deleted successfully!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting image: ' . $e->getMessage());
    }
}
    /**
     * Approve a hostel with notification to landlord
     */
    public function approveHostel($id): RedirectResponse
    {
        try {
            $hostel = Hostel::findOrFail($id);

            $hostel->update([
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            // You can add hostel approval email notification here if needed
            // NotificationService::sendHostelApproval($hostel);

            return redirect()->back()->with('success', 'Hostel approved successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving hostel: ' . $e->getMessage());
        }
    }
/**
 * Update hostel availability
 */
public function updateHostelAvailability(Request $request, $id): RedirectResponse
{
    try {
        $hostel = Hostel::findOrFail($id);
        
        $hostel->update([
            'is_available' => $request->is_available
        ]);
        
        return redirect()->back()->with('success', 'Hostel availability updated successfully!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error updating hostel availability: ' . $e->getMessage());
    }
}
/**
 * Delete a hostel
 */
public function destroyHostel($id): RedirectResponse
{
    try {
        $hostel = Hostel::findOrFail($id);
        
        // Delete associated images from storage
        if ($hostel->images && count($hostel->images) > 0) {
            foreach ($hostel->images as $image) {
                \Storage::delete($image);
            }
        }
        
        $hostel->delete();
        
        return redirect()->route('admin.hostels')->with('success', 'Hostel deleted successfully!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting hostel: ' . $e->getMessage());
    }
}
/**
 * Reject hostel with reason
 */

public function rejectHostel(Request $request, $id): RedirectResponse
{
    try {
        $hostel = Hostel::findOrFail($id);
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $hostel->update([
            'is_approved' => false,
            'is_available' => false,
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
        ]);
        
        // Optional: Send notification to landlord
        // NotificationService::sendHostelRejection($hostel, $validated['rejection_reason']);
        
        return redirect()->back()->with('success', 'Hostel rejected successfully!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error rejecting hostel: ' . $e->getMessage());
    }
}

    /**
     * Show individual booking details
     */
    public function showBooking($id): View
    {
        $booking = Booking::with(['user', 'hostel', 'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateBooking(Request $request, $id): RedirectResponse
    {
        try {
            $booking = Booking::findOrFail($id);

            $validated = $request->validate([
                'booking_status' => 'required|in:pending,confirmed,active,completed,cancelled',
                'payment_status' => 'required|in:pending,paid,failed,refunded',
            ]);

            $booking->update($validated);

            return redirect()->back()->with('success', 'Booking updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating booking: ' . $e->getMessage());
        }
    }

    /**
     * Display all service requests
     */
    public function serviceRequests(): View
    {
        $serviceRequests = ServiceRequest::with(['student', 'serviceProvider.user', 'hostel'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.service-requests.index', compact('serviceRequests'));
    }

    /**
     * Show individual service request details
     */
    public function showServiceRequest($id): View
    {
        $serviceRequest = ServiceRequest::with(['student', 'serviceProvider.user', 'hostel'])
            ->findOrFail($id);

        return view('admin.service-requests.show', compact('serviceRequest'));
    }

    /**
     * Update service request status
     */
    public function updateServiceRequest(Request $request, $id): RedirectResponse
    {
        try {
            $serviceRequest = ServiceRequest::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            ]);

            $serviceRequest->update($validated);

            return redirect()->back()->with('success', 'Service request updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating service request: ' . $e->getMessage());
        }
    }

    /**
     * Display all service providers
     */
    /**
 * Display all service providers
 */
public function serviceProviders(): View
{
    $serviceProviders = ServiceProvider::with(['user', 'serviceRequests'])
        ->withCount(['serviceRequests'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Add these variables that the view expects
    $totalProviders = ServiceProvider::count();
    $verifiedProviders = ServiceProvider::where('is_verified', true)->count();
    $pendingProviders = ServiceProvider::where('is_verified', false)->count();
    $rejectedProviders = 0; // Set to 0 since we don't track rejected providers

    return view('admin.service-providers.index', compact(
        'serviceProviders',
        'totalProviders',
        'verifiedProviders',
        'pendingProviders',
        'rejectedProviders'
    ));
}


    /**
     * Show individual service provider details
     */
    public function showServiceProvider($id): View
    {
        $serviceProvider = ServiceProvider::with(['user', 'serviceRequests.student', 'serviceRequests.hostel'])
            ->withCount(['serviceRequests'])
            ->findOrFail($id);

        return view('admin.service-providers.show', compact('serviceProvider'));
    }

/**
     * Verify a service provider with email notification
     */
    public function verifyServiceProvider($id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $serviceProvider = ServiceProvider::findOrFail($id);
                $serviceProvider->update(['is_verified' => true]);

                // If the user wasn't approved yet, approve them and send notification
                if (!$serviceProvider->user->is_approved) {
                    $serviceProvider->user->update([
                        'is_approved' => true,
                        'approved_at' => now(),
                        'is_active' => true,
                    ]);

                    NotificationService::sendAccountApproval($serviceProvider->user);
                }
            });

            return redirect()->back()->with('success', 'Service provider verified successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error verifying service provider: ' . $e->getMessage());
        }
    }

    /**
     * Display system analytics
     */
    /**
 * Display system analytics
 */
/**
 * Display system analytics
 */
public function analytics(Request $request): View
{
    try {
        $days = $request->get('days', 30);
        
        // User counts by type
        $totalStudents = User::where('user_type', 'student')->count();
        $totalLandlords = User::where('user_type', 'landlord')->count();
        $totalServiceProviders = User::where('user_type', 'service_provider')->count();
        
        // Active users (users who have been active in the last 30 days)
        $totalActiveUsers = User::where('last_activity_at', '>=', now()->subDays(30))
            ->orWhere('updated_at', '>=', now()->subDays(30))
            ->count();
        
        $totalUsers = $totalStudents + $totalLandlords + $totalServiceProviders;
        $activePercentage = $totalUsers > 0 ? round(($totalActiveUsers / $totalUsers) * 100, 1) : 0;
        
        // Growth percentages (last 30 days vs previous 30 days)
        $lastMonthStudents = User::where('user_type', 'student')
            ->where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        $currentMonthStudents = User::where('user_type', 'student')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $studentGrowth = $lastMonthStudents > 0 
            ? round((($currentMonthStudents - $lastMonthStudents) / $lastMonthStudents) * 100, 1)
            : ($currentMonthStudents > 0 ? 100 : 0);
        
        $lastMonthLandlords = User::where('user_type', 'landlord')
            ->where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        $currentMonthLandlords = User::where('user_type', 'landlord')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $landlordGrowth = $lastMonthLandlords > 0 
            ? round((($currentMonthLandlords - $lastMonthLandlords) / $lastMonthLandlords) * 100, 1)
            : ($currentMonthLandlords > 0 ? 100 : 0);
        
        $lastMonthServiceProviders = User::where('user_type', 'service_provider')
            ->where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        $currentMonthServiceProviders = User::where('user_type', 'service_provider')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $serviceProviderGrowth = $lastMonthServiceProviders > 0 
            ? round((($currentMonthServiceProviders - $lastMonthServiceProviders) / $lastMonthServiceProviders) * 100, 1)
            : ($currentMonthServiceProviders > 0 ? 100 : 0);
        
        // Payment statistics
        $totalPayments = Payment::count();
        $totalSuccessfulPayments = Payment::where('status', 'successful')->count();
        $totalPendingPayments = Payment::where('status', 'pending')->count();
        $totalFailedPayments = Payment::where('status', 'failed')->count();
        $totalCancelledPayments = Payment::where('status', 'cancelled')->count();
        
        $totalRevenue = Payment::where('status', 'successful')->sum('amount') ?? 0;
        $pendingAmount = Payment::where('status', 'pending')->sum('amount') ?? 0;
        $failedAmount = Payment::where('status', 'failed')->sum('amount') ?? 0;
        $cancelledAmount = Payment::where('status', 'cancelled')->sum('amount') ?? 0;
        
        $successRate = $totalPayments > 0 ? round(($totalSuccessfulPayments / $totalPayments) * 100, 1) : 0;
        $pendingPercentage = $totalPayments > 0 ? round(($totalPendingPayments / $totalPayments) * 100, 1) : 0;
        $failedPercentage = $totalPayments > 0 ? round(($totalFailedPayments / $totalPayments) * 100, 1) : 0;
        $cancelledPercentage = $totalPayments > 0 ? round(($totalCancelledPayments / $totalPayments) * 100, 1) : 0;
        
        $averagePaymentValue = $totalSuccessfulPayments > 0 ? round($totalRevenue / $totalSuccessfulPayments, 2) : 0;
        $minPaymentValue = Payment::where('status', 'successful')->min('amount') ?? 0;
        $maxPaymentValue = Payment::where('status', 'successful')->max('amount') ?? 0;
        
        // Today's revenue
        $todayRevenue = Payment::where('status', 'successful')
            ->whereDate('completed_at', today())
            ->sum('amount') ?? 0;
        $todayTransactions = Payment::whereDate('created_at', today())->count();
        $todaySuccessful = Payment::where('status', 'successful')
            ->whereDate('completed_at', today())
            ->count();
        
        // This month's revenue
        $monthRevenue = Payment::where('status', 'successful')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('amount') ?? 0;
        $monthTransactions = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthSuccessful = Payment::where('status', 'successful')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();
        
        // Revenue growth
        $lastMonthRevenue = Payment::where('status', 'successful')
            ->whereMonth('completed_at', now()->subMonth()->month)
            ->whereYear('completed_at', now()->subMonth()->year)
            ->sum('amount') ?? 0;
        $currentMonthRevenue = Payment::where('status', 'successful')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('amount') ?? 0;
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($currentMonthRevenue > 0 ? 100 : 0);
        
        // Revenue by source
        $totalBookingRevenue = Payment::where('status', 'successful')
            ->whereHas('booking')
            ->sum('amount') ?? 0;
        $totalServiceRevenue = Payment::where('status', 'successful')
            ->whereHas('serviceRequest')
            ->sum('amount') ?? 0;
        
        // Recent payments
        $recentPayments = Payment::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Payment trends (daily for the selected period)
        $paymentTrends = Payment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Monthly payment summary
        $monthlyPaymentSummary = Payment::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total_payments'),
                DB::raw('SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful_payments'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_payments'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_payments'),
                DB::raw('SUM(CASE WHEN status = "successful" THEN amount ELSE 0 END) as total_amount')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($item) {
                $item->success_rate = $item->total_payments > 0 
                    ? round(($item->successful_payments / $item->total_payments) * 100, 1) 
                    : 0;
                return $item;
            });
        
        // Top paying users
        $topPayingUsers = User::withCount(['payments' => function($query) {
                $query->where('status', 'successful');
            }])
            ->withSum(['payments' => function($query) {
                $query->where('status', 'successful');
            }], 'amount')
            ->having('payments_count', '>', 0)
            ->orderBy('payments_sum_amount', 'desc')
            ->limit(10)
            ->get()
            ->map(function($user) {
                $user->total_spent = $user->payments_sum_amount ?? 0;
                $user->last_payment_date = Payment::where('user_id', $user->id)
                    ->where('status', 'successful')
                    ->latest()
                    ->value('completed_at');
                return $user;
            });
        
        // Top hostels by revenue
        $topHostels = Hostel::with(['landlord'])
            ->withCount(['bookings' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->withSum(['bookings' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->having('bookings_count', '>', 0)
            ->orderBy('bookings_sum_total_amount', 'desc')
            ->limit(10)
            ->get()
            ->map(function($hostel) {
                $hostel->total_revenue = $hostel->bookings_sum_total_amount ?? 0;
                $hostel->average_booking_value = $hostel->bookings_count > 0 
                    ? round($hostel->total_revenue / $hostel->bookings_count, 2) 
                    : 0;
                return $hostel;
            });
        
        // User growth by type (for chart)
        $userGrowthByType = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN user_type = "student" THEN 1 ELSE 0 END) as students'),
                DB::raw('SUM(CASE WHEN user_type = "landlord" THEN 1 ELSE 0 END) as landlords'),
                DB::raw('SUM(CASE WHEN user_type = "service_provider" THEN 1 ELSE 0 END) as service_providers')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Total bookings and service requests
        $totalBookings = Booking::count();
        $totalServiceRequests = ServiceRequest::count();
        
        // Original analytics data for compatibility
        $userGrowth = $userGrowthByType;
        $bookingTrends = collect();
        $serviceRequestTrends = collect();
        $userDistribution = User::selectRaw('user_type, COUNT(*) as count')
            ->groupBy('user_type')
            ->get();
        $bookingStatusDistribution = Booking::selectRaw('booking_status, COUNT(*) as count')
            ->groupBy('booking_status')
            ->get();

        return view('admin.analytics.index', compact(
            'totalStudents',
            'totalLandlords',
            'totalServiceProviders',
            'totalActiveUsers',
            'totalUsers',
            'activePercentage',
            'studentGrowth',
            'landlordGrowth',
            'serviceProviderGrowth',
            'totalPayments',
            'totalSuccessfulPayments',
            'totalPendingPayments',
            'totalFailedPayments',
            'totalCancelledPayments',
            'totalRevenue',
            'pendingAmount',
            'failedAmount',
            'cancelledAmount',
            'successRate',
            'pendingPercentage',
            'failedPercentage',
            'cancelledPercentage',
            'averagePaymentValue',
            'minPaymentValue',
            'maxPaymentValue',
            'todayRevenue',
            'todayTransactions',
            'todaySuccessful',
            'monthRevenue',
            'monthTransactions',
            'monthSuccessful',
            'revenueGrowth',
            'totalBookingRevenue',
            'totalServiceRevenue',
            'recentPayments',
            'paymentTrends',
            'monthlyPaymentSummary',
            'topPayingUsers',
            'topHostels',
            'userGrowthByType',
            'totalBookings',
            'totalServiceRequests',
            'userGrowth',
            'bookingTrends',
            'serviceRequestTrends',
            'userDistribution',
            'bookingStatusDistribution'
        ));

    } catch (\Exception $e) {
        \Log::error('Analytics Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        // Return empty data if there's an error
        return view('admin.analytics.index', [
            'totalStudents' => 0,
            'totalLandlords' => 0,
            'totalServiceProviders' => 0,
            'totalActiveUsers' => 0,
            'totalUsers' => 0,
            'activePercentage' => 0,
            'studentGrowth' => 0,
            'landlordGrowth' => 0,
            'serviceProviderGrowth' => 0,
            'totalPayments' => 0,
            'totalSuccessfulPayments' => 0,
            'totalPendingPayments' => 0,
            'totalFailedPayments' => 0,
            'totalCancelledPayments' => 0,
            'totalRevenue' => 0,
            'pendingAmount' => 0,
            'failedAmount' => 0,
            'cancelledAmount' => 0,
            'successRate' => 0,
            'pendingPercentage' => 0,
            'failedPercentage' => 0,
            'cancelledPercentage' => 0,
            'averagePaymentValue' => 0,
            'minPaymentValue' => 0,
            'maxPaymentValue' => 0,
            'todayRevenue' => 0,
            'todayTransactions' => 0,
            'todaySuccessful' => 0,
            'monthRevenue' => 0,
            'monthTransactions' => 0,
            'monthSuccessful' => 0,
            'revenueGrowth' => 0,
            'totalBookingRevenue' => 0,
            'totalServiceRevenue' => 0,
            'recentPayments' => collect(),
            'paymentTrends' => collect(),
            'monthlyPaymentSummary' => collect(),
            'topPayingUsers' => collect(),
            'topHostels' => collect(),
            'userGrowthByType' => collect(),
            'totalBookings' => 0,
            'totalServiceRequests' => 0,
            'userGrowth' => collect(),
            'bookingTrends' => collect(),
            'serviceRequestTrends' => collect(),
            'userDistribution' => collect(),
            'bookingStatusDistribution' => collect(),
        ])->with('error', 'Some analytics data could not be loaded. Please check your database schema.');
    }
}

    /**
     * Display system settings
     */
    public function systemSettings(): View
    {
        return view('admin.settings');
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request): RedirectResponse
    {
        // Update system settings logic here
        // You can add configuration updates, email settings, etc.

        return redirect()->route('admin.settings')
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Export users data
     */
    public function exportUsers()
    {
        // Implementation for exporting users data
        // You can use Laravel Excel or CSV export here
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Export bookings data
     */
    public function exportBookings()
    {
        // Implementation for exporting bookings data
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Get user statistics for charts
     */
    public function getUserStatistics()
    {
        try {
            $userStats = User::selectRaw('user_type, COUNT(*) as count, DATE(created_at) as date')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('user_type', 'date')
                ->orderBy('date')
                ->get();

            return response()->json($userStats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user statistics'], 500);
        }
    }
    /**
 * Display bulk approval page
 */
/**
 * Display bulk approval page
 */
public function bulkApproval(): View
{
    $pendingUsers = User::where('is_approved', false)
        ->whereIn('user_type', ['landlord', 'service_provider'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return view('admin.users.approval', compact('pendingUsers'));
}

/**
 * Bulk approve users with email notifications
 */
public function bulkApproveUsers(Request $request): RedirectResponse
{
    try {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $approvedCount = 0;

        DB::transaction(function () use ($request, &$approvedCount) {
            $users = User::whereIn('id', $request->user_ids)
                ->where('is_approved', false)
                ->whereIn('user_type', ['landlord', 'service_provider'])
                ->get();

            foreach ($users as $user) {
                $user->update([
                    'is_approved' => true,
                    'approved_at' => now(),
                    'is_active' => true,
                ]);

                // Update service provider verification if applicable
                if ($user->user_type === 'service_provider' && $user->serviceProvider) {
                    $user->serviceProvider->update([
                        'is_verified' => true,
                        'is_available' => true
                    ]);
                }

                $approvedCount++;
            }

            // Send bulk notifications
            NotificationService::sendBulkApprovalNotifications($request->user_ids);
        });

        return redirect()->route('admin.users.bulk-approval')
            ->with('success', "{$approvedCount} users approved successfully and approval emails sent!");

    } catch (\Exception $e) {
        return redirect()->route('admin.users.bulk-approval')
            ->with('error', 'Error bulk approving users: ' . $e->getMessage());
    }
}
}
