<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
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
        @auth
            <button onclick="toggleFavorite({{ $hostel->id }})"
                    class="absolute top-3 right-3 bg-white p-2 rounded-full shadow-md hover:bg-gray-100 transition-colors duration-200">
                <svg class="w-5 h-5 {{ $hostel->is_favorited ? 'text-red-500 fill-current' : 'text-gray-400' }}" 
                     fill="{{ $hostel->is_favorited ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>
        @endauth

        <!-- Rooms Available Badge -->
        @if($hostel->rooms_available > 0)
            <div class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded text-xs font-medium">
                {{ $hostel->rooms_available }} room{{ $hostel->rooms_available > 1 ? 's' : '' }} available
            </div>
        @else
            <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded text-xs font-medium">
                Fully Booked
            </div>
        @endif
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

        <p class="text-gray-600 text-sm mb-3 flex items-center">
            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            {{ $hostel->location }}
        </p>

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

        <!-- Deposit Info -->
        @if($hostel->deposit_amount > 0)
            <div class="mb-3">
                <span class="text-sm text-gray-600">Deposit: </span>
                <span class="text-sm font-medium text-gray-900">KSh {{ number_format($hostel->deposit_amount) }}</span>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <a href="{{ route('student.view-hostel', $hostel->id) }}"
               class="flex-1 bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 text-sm font-medium">
                View Details
            </a>
            @if($hostel->rooms_available > 0)
                <a href="{{ route('student.view-hostel', $hostel->id) }}#booking"
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm font-medium">
                    Book Now
                </a>
            @endif
        </div>
    </div>
</div>

<script>
function toggleFavorite(hostelId) {
    fetch(`/favorites/${hostelId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
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
</script>