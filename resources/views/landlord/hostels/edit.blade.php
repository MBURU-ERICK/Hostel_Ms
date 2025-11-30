<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Edit {{ $hostel->name }} - HostelHub Landlord</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">HostelHub Landlord</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar and Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('landlord.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('landlord.hostels') }}" 
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        My Hostels
                    </a>
                    <a href="{{ route('landlord.bookings') }}" 
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                    <a href="{{ route('landlord.messages') }}" 
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Messages
                    </a>
                    <a href="{{ route('landlord.reviews') }}" 
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Reviews
                    </a>
                    <a href="{{ route('landlord.earnings') }}" 
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Earnings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Hostel</h1>
                        <p class="text-gray-600 mt-2">Update your hostel information and settings</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('landlord.hostels') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Hostels
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Alert -->
            @if($hostel->is_approved)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">Hostel Approved</h4>
                            <p class="text-sm text-green-700">Your hostel is currently visible to students. Changes will require re-approval.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Pending Approval</h4>
                            <p class="text-sm text-yellow-700">Your hostel is under review and not visible to students yet.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Edit Hostel Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
               <form action="{{ route('landlord.hostels.update', $hostel->id) }}" method="POST" enctype="multipart/form-data" id="hostel-edit-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hostel Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hostel Name *
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $hostel->name) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., University View Hostel" required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Location/Area *
                                </label>
                                <input type="text" name="location" id="location" value="{{ old('location', $hostel->location) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Westlands, Nairobi" required>
                                @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Full Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Address *
                                </label>
                                <textarea name="address" id="address" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          placeholder="Enter complete address including street, building, etc." required>{{ old('address', $hostel->address) }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Description *
                                </label>
                                <textarea name="description" id="description" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          placeholder="Describe your hostel, nearby facilities, transportation, etc. (Minimum 50 characters)" required>{{ old('description', $hostel->description) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Minimum 50 characters. Describe what makes your hostel special.</p>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Rooms Section -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Room Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Rent per Month -->
                            <div>
                                <label for="rent_per_month" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rent per Month (KSh) *
                                </label>
                                <input type="number" name="rent_per_month" id="rent_per_month" value="{{ old('rent_per_month', $hostel->rent_per_month) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., 15000" min="0" step="100" required>
                                @error('rent_per_month')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deposit Amount -->
                            <div>
                                <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deposit Amount (KSh) *
                                </label>
                                <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', $hostel->deposit_amount) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., 5000" min="0" step="100" required>
                                @error('deposit_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Rooms -->
                            <div>
                                <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Rooms *
                                </label>
                                <input type="number" name="total_rooms" id="total_rooms" value="{{ old('total_rooms', $hostel->total_rooms) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., 20" min="1" required>
                                @error('total_rooms')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rooms Available -->
                            <div>
                                <label for="rooms_available" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rooms Available *
                                </label>
                                <input type="number" name="rooms_available" id="rooms_available" value="{{ old('rooms_available', $hostel->rooms_available) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., 15" min="0" required>
                                @error('rooms_available')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Amenities & Facilities</h3>
                        <p class="text-sm text-gray-600 mb-4">Select all amenities available in your hostel:</p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $commonAmenities = [
                                    'WiFi', 'Water Supply', 'Electricity', 'Security',
                                    'Laundry', 'Common Room', 'Study Room', 'Kitchen',
                                    'Parking', 'CCTV', 'Generator', 'Hot Water',
                                    'Furnished Rooms', 'Cleaning Service', 'Gym', 'Garden'
                                ];
                                $currentAmenities = old('amenities', $hostel->amenities ?? []);
                            @endphp
                            
                            @foreach($commonAmenities as $amenity)
                                <div class="flex items-center">
                                    <input type="checkbox" name="amenities[]" id="amenity_{{ $loop->index }}" 
                                           value="{{ $amenity }}" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           {{ in_array($amenity, $currentAmenities) ? 'checked' : '' }}>
                                    <label for="amenity_{{ $loop->index }}" class="ml-2 text-sm text-gray-700">
                                        {{ $amenity }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('amenities')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rules & Contact Section -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rules & Contact Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Rules -->
                            <div>
                                <label for="rules" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hostel Rules (Optional)
                                </label>
                                <textarea name="rules" id="rules" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          placeholder="e.g., No smoking, Quiet hours after 10 PM, etc.">{{ old('rules', $hostel->rules) }}</textarea>
                                @error('rules')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Information -->
                            <div class="space-y-4">
                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Phone *
                                    </label>
                                    <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $hostel->contact_phone) }}"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., +254712345678" required>
                                    @error('contact_phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Email *
                                    </label>
                                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $hostel->contact_email) }}"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., contact@hostel.com" required>
                                    @error('contact_email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Hostel Images</h3>
                        
                        <!-- Current Images -->
                        @if($hostel->images && count($hostel->images) > 0)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Current Images</h4>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    @foreach($hostel->images as $index => $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image) }}" 
                                                 alt="Hostel image {{ $index + 1 }}"
                                                 class="w-full h-24 object-cover rounded-lg">
                                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ Storage::url($image) }}" 
                                                   target="_blank"
                                                   class="text-white text-xs bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded mr-1">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Upload new images below to replace current ones</p>
                            </div>
                        @endif

                        <!-- New Images Upload -->
                        <p class="text-sm text-gray-600 mb-4">Upload new photos of your hostel (max 5 images, 2MB each):</p>
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" 
                             x-data="{ files: [] }" 
                             @drop.prevent="files = Array.from($event.dataTransfer.files)"
                             @dragover.prevent="$event.dataTransfer.dropEffect = 'move'">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-600 mb-2">Drag and drop your images here, or click to browse</p>
                            <p class="text-xs text-gray-500 mb-4">PNG, JPG, GIF up to 2MB each</p>
                            <input type="file" name="images[]" id="images" multiple 
                                   class="hidden" 
                                   accept="image/jpeg,image/png,image/gif"
                                   @change="files = Array.from($event.target.files)">
                            <button type="button" 
                                    @click="document.getElementById('images').click()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Select New Images
                            </button>
                            
                            <!-- Selected files preview -->
                            <template x-if="files.length > 0">
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Selected files:</h4>
                                    <ul class="text-sm text-gray-600">
                                        <template x-for="file in files" :key="file.name">
                                            <li x-text="file.name"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                        @error('images.*')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                            @if($hostel->is_available)
            <form action="{{ route('landlord.hostels.toggle-availability', $hostel->id) }}" method="POST" id="toggle-unavailable-form">
                @csrf
                @method('PATCH')
            </form>
        @else
            <form action="{{ route('landlord.hostels.toggle-availability', $hostel->id) }}" method="POST" id="toggle-available-form">
                @csrf
                @method('PATCH')
            </form>
        @endif

                    <!-- Form Actions -->
<div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    <a href="{{ route('landlord.hostels') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                        Cancel
                    </a>
                    @if($hostel->is_available)
                        <button type="submit" 
                                form="toggle-unavailable-form"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Make Unavailable
                        </button>
                    @else
                        <button type="submit" 
                                form="toggle-available-form"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Make Available
                        </button>
                    @endif
                </div>
                <button type="submit" 
                        form="hostel-edit-form"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Hostel
                </button>
            </div>
        </div>


            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">💡 Update Guidelines</h3>
                <ul class="text-blue-800 text-sm space-y-1">
                    <li>• Any changes will require admin approval before being visible to students</li>
                    <li>• Keep your contact information up to date for student inquiries</li>
                    <li>• Update room availability regularly to avoid overbooking</li>
                    <li>• Adding new, high-quality photos can increase booking rates</li>
                    <li>• Set your hostel as unavailable during maintenance or renovations</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate available rooms based on total rooms
        document.addEventListener('DOMContentLoaded', function() {
            const totalRoomsInput = document.getElementById('total_rooms');
            const availableRoomsInput = document.getElementById('rooms_available');
            
            totalRoomsInput.addEventListener('input', function() {
                const totalRooms = parseInt(this.value) || 0;
                const currentAvailable = parseInt(availableRoomsInput.value) || 0;
                
                if (currentAvailable > totalRooms) {
                    availableRoomsInput.value = totalRooms;
                }
                
                availableRoomsInput.max = totalRooms;
            });
        });
    </script>
</body>
</html>