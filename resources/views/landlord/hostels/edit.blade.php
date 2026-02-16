<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Edit {{ $hostel->name }} - HostelHub Landlord</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('landlord.hostels') }}"
                       class="flex items-center px-4 py-3 text-blue-600 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                        <i class="fas fa-building w-5 h-5 mr-3"></i>
                        My Hostels
                    </a>
                    <a href="{{ route('landlord.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                        Bookings
                    </a>
                    <a href="{{ route('landlord.messages.index') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-envelope w-5 h-5 mr-3"></i>
                        Messages
                    </a>
                    <a href="{{ route('landlord.reviews') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-star w-5 h-5 mr-3"></i>
                        Reviews
                    </a>
                    <a href="{{ route('landlord.earnings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-money-bill w-5 h-5 mr-3"></i>
                        Earnings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center" id="successMessage">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center" id="errorMessage">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="font-medium">Please fix the following errors:</div>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Hostels
                        </a>
                        
                    </div>
                </div>
            </div>

            <!-- Status Alert -->
            @if($hostel->is_approved)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">✓ Hostel Approved</h4>
                            <p class="text-sm text-green-700">Your hostel is currently visible to students. Changes will require re-approval.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-yellow-600 mr-3 text-xl"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">⏳ Pending Approval</h4>
                            <p class="text-sm text-yellow-700">Your hostel is under review and not visible to students yet.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Edit Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- UPDATE HOSTEL DETAILS FORM - USES PUT METHOD -->
                <form action="{{ route('landlord.hostels.update', $hostel->id) }}" method="POST" enctype="multipart/form-data" id="hostel-edit-form">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Basic Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hostel Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hostel Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $hostel->name) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                       placeholder="e.g., University View Hostel" 
                                       required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location/Area -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Location/Area <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="location" 
                                       id="location" 
                                       value="{{ old('location', $hostel->location) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                                       placeholder="e.g., Westlands, Nairobi" 
                                       required>
                                @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Full Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Address <span class="text-red-500">*</span>
                                </label>
                                <textarea name="address" 
                                          id="address" 
                                          rows="2"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                          placeholder="Enter complete address including street, building, etc."
                                          required>{{ old('address', $hostel->address) }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                          placeholder="Describe your hostel, nearby facilities, transportation, etc. (Minimum 50 characters)"
                                          required>{{ old('description', $hostel->description) }}</textarea>
                                <div class="flex justify-between mt-1">
                                    <p class="text-xs text-gray-500">Minimum 50 characters</p>
                                    <p class="text-xs text-gray-500" id="description-counter">0/50</p>
                                </div>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Room Information -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-tag text-green-600 mr-2"></i>
                            Pricing & Room Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Rent per Month -->
                            <div>
                                <label for="rent_per_month" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rent/Month (KSh) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">KSh</span>
                                    <input type="number" 
                                           name="rent_per_month" 
                                           id="rent_per_month" 
                                           value="{{ old('rent_per_month', $hostel->rent_per_month) }}"
                                           class="w-full border border-gray-300 rounded-lg pl-12 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rent_per_month') border-red-500 @enderror"
                                           placeholder="15000" 
                                           min="0" 
                                           step="100" 
                                           required>
                                </div>
                                @error('rent_per_month')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deposit Amount -->
                            <div>
                                <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deposit (KSh) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">KSh</span>
                                    <input type="number" 
                                           name="deposit_amount" 
                                           id="deposit_amount" 
                                           value="{{ old('deposit_amount', $hostel->deposit_amount) }}"
                                           class="w-full border border-gray-300 rounded-lg pl-12 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('deposit_amount') border-red-500 @enderror"
                                           placeholder="5000" 
                                           min="0" 
                                           step="100" 
                                           required>
                                </div>
                                @error('deposit_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Rooms -->
                            <div>
                                <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Rooms <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="total_rooms" 
                                       id="total_rooms" 
                                       value="{{ old('total_rooms', $hostel->total_rooms) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('total_rooms') border-red-500 @enderror"
                                       placeholder="20" 
                                       min="1" 
                                       required>
                                @error('total_rooms')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rooms Available -->
                            <div>
                                <label for="rooms_available" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rooms Available <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="rooms_available" 
                                       id="rooms_available" 
                                       value="{{ old('rooms_available', $hostel->rooms_available) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rooms_available') border-red-500 @enderror"
                                       placeholder="15" 
                                       min="0" 
                                       required>
                                @error('rooms_available')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-wifi text-purple-600 mr-2"></i>
                            Amenities & Facilities
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Select all amenities available in your hostel:</p>

                        @php
                            $commonAmenities = [
                                'WiFi', 'Water Supply', 'Electricity', 'Security', 'CCTV',
                                'Laundry', 'Common Room', 'Study Room', 'Kitchen', 'Dining Hall',
                                'Parking', 'Generator', 'Hot Water', 'Furnished Rooms', 'Bedding',
                                'Cleaning Service', 'Gym', 'Garden', 'Library', 'TV Room',
                                'Elevator', 'Wheelchair Access', 'Pet Friendly', 'Smoking Area'
                            ];
                            $currentAmenities = old('amenities', $hostel->amenities ?? []);
                            if (is_string($currentAmenities)) {
                                $currentAmenities = json_decode($currentAmenities, true) ?? [];
                            }
                        @endphp

                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($commonAmenities as $amenity)
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="amenities[]" 
                                           id="amenity_{{ Str::slug($amenity) }}"
                                           value="{{ $amenity }}" 
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           {{ in_array($amenity, $currentAmenities) ? 'checked' : '' }}>
                                    <label for="amenity_{{ Str::slug($amenity) }}" class="ml-2 text-sm text-gray-700">
                                        {{ $amenity }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Custom Amenities -->
                        <div class="mt-4">
                            <label for="custom_amenities" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Amenities (comma separated)
                            </label>
                            <input type="text" 
                                   id="custom_amenities" 
                                   name="custom_amenities" 
                                   value="{{ old('custom_amenities') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Pool, Sauna, Tennis Court">
                            <p class="text-xs text-gray-500 mt-1">Add any amenities not listed above</p>
                        </div>
                        
                        @error('amenities')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rules & Contact Information -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-gavel text-orange-600 mr-2"></i>
                            Rules & Contact Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hostel Rules -->
                            <div>
                                <label for="rules" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hostel Rules
                                </label>
                                <textarea name="rules" 
                                          id="rules" 
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          placeholder="e.g., No smoking, Quiet hours after 10 PM, No guests after 11 PM, etc.">{{ old('rules', $hostel->rules) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">List important rules students should know</p>
                                @error('rules')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Information -->
                            <div class="space-y-4">
                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                           name="contact_phone" 
                                           id="contact_phone" 
                                           value="{{ old('contact_phone', $hostel->contact_phone) }}"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contact_phone') border-red-500 @enderror"
                                           placeholder="+254712345678" 
                                           required>
                                    @error('contact_phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           name="contact_email" 
                                           id="contact_email" 
                                           value="{{ old('contact_email', $hostel->contact_email) }}"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contact_email') border-red-500 @enderror"
                                           placeholder="contact@hostel.com" 
                                           required>
                                    @error('contact_email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-images text-indigo-600 mr-2"></i>
                            Hostel Images
                        </h3>

                        <!-- Current Images -->
                        @if($hostel->images && count($hostel->images) > 0)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Current Images</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach($hostel->images as $index => $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image) }}"
                                                 alt="Hostel image {{ $index + 1 }}"
                                                 class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ Storage::url($image) }}"
                                                   target="_blank"
                                                   class="text-white text-xs bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded mr-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button"
                                                        onclick="deleteImage('{{ $image }}', {{ $hostel->id }})"
                                                        class="text-white text-xs bg-red-600 hover:bg-red-700 px-2 py-1 rounded">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Upload new images below to add more. Maximum 5 images total.
                                </p>
                            </div>
                        @endif

                        <!-- New Images Upload -->
                        <div x-data="{ imagePreviews: [] }" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                            <input type="file" 
                                   name="images[]" 
                                   id="images" 
                                   multiple 
                                   accept="image/jpeg,image/png,image/jpg,image/gif"
                                   class="hidden"
                                   @change="imagePreviews = Array.from($event.target.files).map(file => URL.createObjectURL(file))">
                            
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-2">Drag and drop your images here, or <span class="text-blue-600 cursor-pointer hover:text-blue-800" @click="document.getElementById('images').click()">click to browse</span></p>
                            <p class="text-xs text-gray-500 mb-4">PNG, JPG, GIF up to 2MB each (Max 5 images)</p>
                            
                            <!-- Image Previews -->
                            <template x-if="imagePreviews.length > 0">
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2 text-left">New Images Preview:</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <template x-for="(preview, index) in imagePreviews" :key="index">
                                            <div class="relative">
                                                <img :src="preview" class="w-full h-20 object-cover rounded-lg border border-gray-200">
                                                <span class="absolute -top-2 -right-2 bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                                                      x-text="index + 1"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        @error('images.*')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Hostel Details
                                </button>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    Changes will require admin approval
                                </p>
                            </div>
                            <a href="{{ route('landlord.hostels') }}"
                               class="text-gray-600 hover:text-gray-800 px-4 py-2">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Danger Zone - Separate Section for Destructive Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-red-200">
                <div class="p-6 border-b border-red-200 bg-red-50">
                    <h3 class="text-lg font-semibold text-red-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Toggle Availability</h4>
                            <p class="text-sm text-gray-600">
                                Current status: 
                                <span class="font-semibold {{ $hostel->is_available ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $hostel->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </p>
                        </div>
                        
                        <!-- TOGGLE AVAILABILITY FORM - USES PATCH METHOD to CORRECT ROUTE -->
                        @if($hostel->is_available)
                            <form action="{{ route('landlord.hostels.toggle-availability', $hostel->id) }}" 
                                  method="POST" 
                                  id="toggle-unavailable-form"
                                  class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to make this hostel unavailable? Students will not be able to book it.')"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                                    <i class="fas fa-pause-circle mr-2"></i>
                                    Make Unavailable
                                </button>
                            </form>
                        @else
                            <form action="{{ route('landlord.hostels.toggle-availability', $hostel->id) }}" 
                                  method="POST" 
                                  id="toggle-available-form"
                                  class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to make this hostel available? Students will be able to book it.')"
                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Make Available
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 my-6"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Delete Hostel</h4>
                            <p class="text-sm text-gray-600">
                                Once you delete a hostel, there is no going back. All bookings and reviews will be permanently removed.
                            </p>
                        </div>
                        <a href="{{ route('landlord.hostels.delete', $hostel->id) }}"
                           class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete Hostel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
                    Update Guidelines
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Any changes to hostel details will require admin approval before being visible to students</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Keep your contact information up to date for student inquiries</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Update room availability regularly to avoid overbooking</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Adding new, high-quality photos can increase booking rates by up to 40%</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Set your hostel as unavailable during maintenance or renovations</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Toggle availability is instant and doesn't require approval</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Image Form (Hidden) -->
    <form id="delete-image-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 5000);

        // Description character counter
        document.addEventListener('DOMContentLoaded', function() {
            const description = document.getElementById('description');
            const counter = document.getElementById('description-counter');
            
            if (description && counter) {
                const updateCounter = () => {
                    const length = description.value.length;
                    counter.textContent = `${length}/50`;
                    counter.style.color = length >= 50 ? 'green' : 'red';
                };
                
                description.addEventListener('input', updateCounter);
                updateCounter(); // Initial update
            }
        });

        // Validate rooms
        document.addEventListener('DOMContentLoaded', function() {
            const totalRooms = document.getElementById('total_rooms');
            const availableRooms = document.getElementById('rooms_available');

            if (totalRooms && availableRooms) {
                const validateRooms = () => {
                    const total = parseInt(totalRooms.value) || 0;
                    const available = parseInt(availableRooms.value) || 0;
                    
                    if (available > total) {
                        availableRooms.value = total;
                    }
                    
                    availableRooms.max = total;
                };

                totalRooms.addEventListener('input', validateRooms);
                availableRooms.addEventListener('input', validateRooms);
                validateRooms(); // Initial validation
            }
        });

        // Delete image function
        function deleteImage(imagePath, hostelId) {
            if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                const form = document.getElementById('delete-image-form');
                form.action = `/landlord/hostels/${hostelId}/images`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="image_path" value="${imagePath}">
                `;
                form.submit();
            }
        }

        // Prevent double form submission
        document.getElementById('hostel-edit-form')?.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Updating...
                `;
            }
        });

        // Custom amenities handling
        document.getElementById('hostel-edit-form')?.addEventListener('submit', function(e) {
            const customAmenities = document.getElementById('custom_amenities')?.value;
            if (customAmenities && customAmenities.trim() !== '') {
                const amenities = customAmenities.split(',').map(item => item.trim()).filter(item => item !== '');
                
                // Add hidden inputs for each custom amenity
                amenities.forEach(amenity => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'amenities[]';
                    input.value = amenity;
                    this.appendChild(input);
                });
            }
        });

        // Confirm before leaving with unsaved changes
        let formChanged = false;
        
        document.getElementById('hostel-edit-form')?.addEventListener('input', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        document.getElementById('hostel-edit-form')?.addEventListener('submit', function() {
            formChanged = false;
        });
    </script>

    <style>
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Loading spinner animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        /* Hover effects */
        .hover\:shadow-md:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</body>
</html>