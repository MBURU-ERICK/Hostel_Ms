<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $hostel->name }} - Hostel Management System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .amenity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.search-hostels') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        ← Back to Search
                    </a>
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Hostel Header -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $hostel->name }}</h1>
                            <div class="flex items-center mt-2">
                                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-gray-600">{{ $hostel->location }}</span>
                            </div>
                        </div>
                        
                        <!-- Favorite Button - Moved here for better placement -->
                        <div x-data="{ isFavorited: {{ $hostel->is_favorited ? 'true' : 'false' }} }" class="ml-4">
                            <button @click="toggleFavorite({{ $hostel->id }})"
                                    class="flex items-center space-x-2 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                                    :class="isFavorited ? 'border-red-300 bg-red-50' : ''">
                                <svg class="w-5 h-5" 
                                     :class="isFavorited ? 'text-red-500 fill-current' : 'text-gray-400'" 
                                     :fill="isFavorited ? 'currentColor' : 'none'" 
                                     stroke="currentColor" 
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="text-sm font-medium" x-text="isFavorited ? 'Favorited' : 'Add to Favorites'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Price and Status -->
                    <div class="flex justify-between items-center">
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600">KSh {{ number_format($hostel->rent_per_month) }}/month</div>
                            <div class="text-sm text-gray-500">Deposit: KSh {{ number_format($hostel->deposit_amount) }}</div>
                        </div>
                        
                        <!-- Status Badges -->
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                                ✅ Approved
                            </span>
                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                🏠 {{ $hostel->rooms_available }} Rooms Available
                            </span>
                            <span class="bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full">
                                📞 {{ $hostel->contact_phone }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Hostel Image -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-center text-gray-500 mt-2">Hostel images coming soon</p>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $hostel->description }}</p>
                    </div>

                    <!-- Amenities -->
                    @if($hostel->amenities)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Amenities & Facilities</h2>
                        <div class="amenity-grid">
                            @php
                                $amenities = is_array($hostel->amenities) ? $hostel->amenities : json_decode($hostel->amenities, true);
                            @endphp
                            @if($amenities && count($amenities) > 0)
                                @foreach($amenities as $amenity)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $amenity }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-500">No amenities listed</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Rules -->
                    @if($hostel->rules)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">House Rules</h2>
                        <div class="prose prose-gray max-w-none">
                            <p class="text-gray-700 whitespace-pre-line">{{ $hostel->rules }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Reviews Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Reviews & Ratings</h2>
                            
                            <!-- Review Button -->
                            @auth
                                @if(auth()->user()->hasBookedHostel($hostel->id))
                                    @php
                                        $booking = auth()->user()->getBookingForHostel($hostel->id);
                                        $hasReviewed = \App\Models\Review::where('booking_id', $booking->id)->exists();
                                    @endphp
                                    
                                    @if(!$hasReviewed)
                                        <a href="{{ route('reviews.create', $booking->id) }}"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                            Write a Review
                                        </a>
                                    @else
                                        <span class="text-green-600 font-medium">You've already reviewed this hostel</span>
                                    @endif
                                @endif
                            @endauth
                        </div>

                        <!-- Overall Rating -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <div class="text-5xl font-bold text-gray-900">{{ number_format($hostel->average_rating, 1) }}</div>
                                    <div class="text-yellow-400 text-xl mt-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($hostel->average_rating))
                                                ⭐
                                            @elseif($i - 0.5 <= $hostel->average_rating)
                                                ⭐
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-gray-600 mt-2">{{ $hostel->total_reviews }} reviews</div>
                                </div>
                                
                                <div class="md:col-span-2">
                                    @foreach([5,4,3,2,1] as $stars)
                                        @php
                                            $count = $hostel->rating_breakdown[$stars] ?? 0;
                                            $percentage = $hostel->total_reviews > 0 ? ($count / $hostel->total_reviews) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center mb-2">
                                            <span class="text-sm text-gray-600 w-8">{{ $stars }}★</span>
                                            <div class="flex-1 mx-2">
                                                <div class="bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-12 text-right">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Reviews List -->
                        @if($hostel->approvedReviews->count() > 0)
                            <div class="space-y-4">
                                @foreach($hostel->approvedReviews()->with('user')->latest()->take(5)->get() as $review)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $review->user->name }}</h3>
                                                <div class="text-yellow-400 mt-1">
                                                    {{ str_repeat('⭐', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    </div>
                                @endforeach
                            </div>

                            @if($hostel->total_reviews > 5)
                                <div class="text-center mt-6">
                                    <a href="{{ route('reviews.hostel', $hostel->id) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                        View all {{ $hostel->total_reviews }} reviews
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                                <p class="text-gray-500">Be the first to review this hostel!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Similar Hostels -->
                    @if($similarHostels->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Similar Hostels in {{ $hostel->location }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($similarHostels as $similar)
                                <a href="{{ route('student.view-hostel', $similar->id) }}"
                                   class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all">
                                    <h3 class="font-semibold text-gray-900">{{ $similar->name }}</h3>
                                    <p class="text-blue-600 font-semibold mt-1">KSh {{ number_format($similar->rent_per_month) }}/month</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $similar->rooms_available }} rooms available</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>

                        <!-- Book Now Button -->
                        <button onclick="openBookingModal()"
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-semibold mb-3">
                                📅 Book This Hostel
                        </button>

                        <!-- Contact Landlord -->
                        <div class="space-y-3">
                            <a href="tel:{{ $hostel->contact_phone }}"
                               class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Call Landlord
                            </a>

                            @if($hostel->contact_email)
                            <a href="mailto:{{ $hostel->contact_email }}"
                               class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email Landlord
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Hostel Details -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hostel Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Monthly Rent:</span>
                                <span class="font-semibold">KSh {{ number_format($hostel->rent_per_month) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Deposit:</span>
                                <span class="font-semibold">KSh {{ number_format($hostel->deposit_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Available Rooms:</span>
                                <span class="font-semibold">{{ $hostel->rooms_available }} of {{ $hostel->total_rooms }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Location:</span>
                                <span class="font-semibold text-right">{{ $hostel->location }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Landlord Information -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Landlord Information</h3>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $hostel->landlord->name }}</p>
                                <p class="text-sm text-gray-600">Verified Landlord</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Book {{ $hostel->name }}</h3>
                    <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('student.book-hostel', $hostel->id) }}">
                    @csrf

                    <div class="space-y-4">
                        <!-- Duration -->
                        <div>
                            <label for="duration_months" class="block text-sm font-medium text-gray-700">Duration (Months)</label>
                            <select id="duration_months" name="duration_months" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} month{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Check-in Date -->
                        <div>
                            <label for="check_in_date" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                            <input type="date" id="check_in_date" name="check_in_date" required
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <!-- Special Requests -->
                        <div>
                            <label for="special_requests" class="block text-sm font-medium text-gray-700">Special Requests (Optional)</label>
                            <textarea id="special_requests" name="special_requests" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Any special requirements or requests..."></textarea>
                        </div>

                        <!-- Cost Summary -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Cost Summary</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Monthly Rent:</span>
                                    <span>KSh <span id="monthlyRent">{{ number_format($hostel->rent_per_month) }}</span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Security Deposit:</span>
                                    <span>KSh {{ number_format($hostel->deposit_amount) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold border-t pt-2">
                                    <span>Total Amount:</span>
                                    <span>KSh <span id="totalAmount">{{ number_format($hostel->rent_per_month + $hostel->deposit_amount) }}</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeBookingModal()"
                                class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Update total amount when duration changes
        document.getElementById('duration_months').addEventListener('change', function() {
            const monthlyRent = {{ $hostel->rent_per_month }};
            const deposit = {{ $hostel->deposit_amount }};
            const duration = parseInt(this.value);
            const totalAmount = (monthlyRent * duration) + deposit;

            document.getElementById('totalAmount').textContent = totalAmount.toLocaleString();
        });

        function openBookingModal() {
            document.getElementById('bookingModal').classList.remove('hidden');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target === modal) {
                closeBookingModal();
            }
        }

        // Favorite functionality
        function toggleFavorite(hostelId) {
            fetch(`/favorites/${hostelId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update Alpine.js state
                    const event = new CustomEvent('favorite-updated', { 
                        detail: { 
                            hostelId: hostelId, 
                            isFavorited: data.is_favorited 
                        } 
                    });
                    window.dispatchEvent(event);
                    
                    // Show success message
                    alert(data.message);
                    
                    // Reload to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update favorites. Please try again.');
            });
        }

        // Listen for favorite updates to update Alpine.js state
        window.addEventListener('favorite-updated', function(event) {
            // This will automatically update the Alpine.js component
            console.log('Favorite updated:', event.detail);
        });
    </script>
</body>
</html>