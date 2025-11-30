<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Search Hostels - Hostel Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.dashboard') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Find Your Perfect Hostel</h1>
                <p class="text-gray-600 mt-2">Search from our wide selection of approved hostels</p>
            </div>

            <!-- Advanced Search Form -->
            <div x-data="advancedSearch()" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form action="{{ route('student.search-hostels') }}" method="GET" id="searchForm">
                    <!-- Basic Search Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <!-- Location Search -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Location
                            </label>
                            <input type="text" 
                                   name="location" 
                                   id="location"
                                   value="{{ request('location') }}"
                                   placeholder="Enter city or area..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label for="min_price" class="block text-sm font-medium text-gray-700 mb-2">
                                Monthly Rent
                            </label>
                            <div class="flex space-x-2">
                                <input type="number" 
                                       name="min_price" 
                                       id="min_price"
                                       value="{{ request('min_price') }}"
                                       placeholder="Min"
                                       class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <input type="number" 
                                       name="max_price" 
                                       id="max_price"
                                       value="{{ request('max_price') }}"
                                       placeholder="Max"
                                       class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Room Type -->
                        <div>
                            <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Room Type
                            </label>
                            <select name="room_type" 
                                    id="room_type"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any Type</option>
                                <option value="single" {{ request('room_type') == 'single' ? 'selected' : '' }}>Single Room</option>
                                <option value="shared" {{ request('room_type') == 'shared' ? 'selected' : '' }}>Shared Room</option>
                                <option value="apartment" {{ request('room_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">
                                Sort By
                            </label>
                            <select name="sort_by" 
                                    id="sort_by"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="price_low" {{ request('sort_by') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort_by') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </div>
                    </div>

                    <!-- Advanced Filters Toggle -->
                    <div class="mb-4">
                        <button type="button" 
                                @click="showAdvanced = !showAdvanced"
                                class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-2 transition-transform duration-200" 
                                 :class="showAdvanced ? 'rotate-180' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            Advanced Filters
                        </button>
                    </div>

                    <!-- Advanced Filters -->
                    <div x-show="showAdvanced" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-lg border border-gray-200 mb-6">
                        
                        <!-- Amenities -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Amenities
                            </label>
                            <div class="space-y-2">
                                @php
                                    $commonAmenities = ['WiFi', 'Parking', 'Security', 'Laundry', 'Kitchen', 'Study Room', 'Gym', 'Swimming Pool'];
                                @endphp
                                @foreach($commonAmenities as $amenity)
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="amenities[]" 
                                               value="{{ $amenity }}"
                                               {{ in_array($amenity, request('amenities', [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $amenity }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Minimum Rating
                            </label>
                            <div class="space-y-2">
                                @foreach([5, 4, 3, 2, 1] as $rating)
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="min_rating" 
                                               value="{{ $rating }}"
                                               {{ request('min_rating') == $rating ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rating)
                                                    ⭐
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                            & above
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Additional Filters -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Additional Filters
                            </label>
                            <div class="space-y-3">
                                <!-- Deposit Amount -->
                                <div>
                                    <label for="max_deposit" class="block text-xs text-gray-600 mb-1">
                                        Max Deposit Amount
                                    </label>
                                    <input type="number" 
                                           name="max_deposit" 
                                           id="max_deposit"
                                           value="{{ request('max_deposit') }}"
                                           placeholder="e.g., 5000"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Available Rooms -->
                                <div>
                                    <label for="min_rooms" class="block text-xs text-gray-600 mb-1">
                                        Minimum Rooms Available
                                    </label>
                                    <input type="number" 
                                           name="min_rooms" 
                                           id="min_rooms"
                                           value="{{ request('min_rooms') }}"
                                           placeholder="e.g., 1"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Instant Booking -->
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="instant_booking" 
                                           value="1"
                                           {{ request('instant_booking') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Available for Instant Booking</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center">
                        <div>
                            @if(request()->anyFilled(['location', 'min_price', 'max_price', 'room_type', 'amenities', 'min_rating']))
                                <a href="{{ route('student.search-hostels') }}" 
                                   class="text-gray-600 hover:text-gray-800 font-medium">
                                    Clear All Filters
                                </a>
                            @endif
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" 
                                    @click="resetForm()"
                                    class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 font-medium">
                                Reset
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                Search Hostels
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Results -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Results Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                @if($hostels->count() > 0)
                                    Found {{ $hostels->total() }} hostels
                                @else
                                    No hostels found
                                @endif
                            </h2>
                            @if(request()->anyFilled(['location', 'min_price', 'max_price', 'room_type', 'amenities', 'min_rating']))
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @if(request('location'))
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                            Location: {{ request('location') }}
                                        </span>
                                    @endif
                                    @if(request('min_price') || request('max_price'))
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                            Price: 
                                            {{ request('min_price') ? 'KSh ' . number_format(request('min_price')) : 'Any' }}
                                            - 
                                            {{ request('max_price') ? 'KSh ' . number_format(request('max_price')) : 'Any' }}
                                        </span>
                                    @endif
                                    @if(request('min_rating'))
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                            Rating: {{ request('min_rating') }}+ stars
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Results Per Page -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Show:</span>
                            <select onchange="updatePerPage(this.value)" 
                                    class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Hostels Grid -->
                @if($hostels->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach($hostels as $hostel)
                            @include('student.partials.hostel-card', ['hostel' => $hostel])
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $hostels->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- No Results -->
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hostels found</h3>
                        <p class="text-gray-500 mb-4">Try adjusting your search criteria or browse all hostels.</p>
                        <a href="{{ route('student.search-hostels') }}" 
                           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                            Browse All Hostels
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function advancedSearch() {
            return {
                showAdvanced: {{ request()->anyFilled(['amenities', 'min_rating', 'max_deposit', 'min_rooms', 'instant_booking']) ? 'true' : 'false' }},
                
                resetForm() {
                    // Reset all form fields
                    document.getElementById('searchForm').reset();
                    // Clear URL parameters and submit
                    window.location.href = "{{ route('student.search-hostels') }}";
                }
            }
        }

        function updatePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            window.location.href = url.toString();
        }

        // Auto-submit form when certain filters change
        document.addEventListener('DOMContentLoaded', function() {
            const autoSubmitElements = document.querySelectorAll('#room_type, #sort_by, [name="min_rating"]');
            autoSubmitElements.forEach(element => {
                element.addEventListener('change', function() {
                    document.getElementById('searchForm').submit();
                });
            });
        });
    </script>
</body>
</html>