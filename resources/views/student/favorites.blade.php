<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Favorites - Hostel Management System</title>

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
                    <a href="{{ route('student.search-hostels') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Search Hostels
                    </a>
                    <a href="{{ route('student.dashboard') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Favorite Hostels</h1>
                <p class="text-gray-600 mt-2">Your saved hostels for easy access</p>
            </div>

            @if($favorites->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($favorites as $hostel)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Hostel Image -->
                            <div class="h-48 bg-gray-200 relative">
                                @if($hostel->images && count($hostel->images) > 0)
                                    <img src="{{ asset('storage/' . $hostel->images[0]) }}" 
                                         alt="{{ $hostel->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Favorite Button -->
                                <button onclick="toggleFavorite({{ $hostel->id }})"
                                        class="absolute top-3 right-3 bg-white p-2 rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Hostel Info -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $hostel->name }}</h3>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-blue-600">KSh {{ number_format($hostel->rent_per_month) }}</div>
                                        <div class="text-sm text-gray-500">per month</div>
                                    </div>
                                </div>

                                <p class="text-gray-600 text-sm mb-3">{{ $hostel->location }}</p>

                                <!-- Rating -->
                                <div class="flex items-center mb-3">
                                    <div class="flex text-yellow-400 mr-2">
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
                                    <span class="text-sm text-gray-600">
                                        {{ number_format($hostel->average_rating, 1) }} ({{ $hostel->total_reviews }} reviews)
                                    </span>
                                </div>

                                <!-- Amenities Preview -->
                                @if($hostel->amenities && count($hostel->amenities) > 0)
                                    <div class="flex flex-wrap gap-1 mb-4">
                                        @foreach(array_slice($hostel->amenities, 0, 3) as $amenity)
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                {{ $amenity }}
                                            </span>
                                        @endforeach
                                        @if(count($hostel->amenities) > 3)
                                            <span class="text-gray-500 text-xs">+{{ count($hostel->amenities) - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('student.view-hostel', $hostel->id) }}"
                                       class="flex-1 bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 text-sm font-medium">
                                        View Details
                                    </a>
                                    <button onclick="removeFavorite({{ $hostel->id }})"
                                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm font-medium">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $favorites->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No favorite hostels yet</h3>
                    <p class="text-gray-500 mb-6">Start exploring hostels and add them to your favorites for easy access.</p>
                    <a href="{{ route('student.search-hostels') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        Browse Hostels
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleFavorite(hostelId) {
            fetch(`/favorites/${hostelId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect changes
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function removeFavorite(hostelId) {
            if (confirm('Remove this hostel from favorites?')) {
                fetch(`/favorites/${hostelId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>