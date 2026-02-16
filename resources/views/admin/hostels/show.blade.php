<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $hostel->name }} - HostelHub Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-indigo-600 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-white">HostelHub Admin</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-indigo-100">Administrator</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-indigo-100 hover:text-white bg-indigo-700 px-3 py-1 rounded">
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
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.hostels') }}"
                       class="flex items-center px-4 py-3 text-gray-700 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg">
                        <i class="fas fa-building w-5 h-5 mr-3"></i>
                        Hostels
                    </a>
                    <a href="{{ route('admin.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                        Bookings
                    </a>
                    <a href="{{ route('admin.service-requests') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tools w-5 h-5 mr-3"></i>
                        Service Requests
                    </a>
                    <a href="{{ route('admin.service-providers') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-user-cog w-5 h-5 mr-3"></i>
                        Service Providers
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                        Analytics
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" id="successMessage">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" id="errorMessage">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6" id="infoMessage">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header with Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.hostels') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Hostels
                </a>
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $hostel->name }}</h1>
                        <p class="text-gray-600 mt-2">{{ $hostel->location }}</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-xs px-2 py-1 rounded-full {{ $hostel->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $hostel->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full {{ $hostel->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $hostel->is_approved ? 'Approved' : 'Pending Approval' }}
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                {{ $hostel->bookings_count ?? 0 }} Bookings
                            </span>
                            @if($hostel->rejection_reason)
                                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800 cursor-pointer" onclick="showRejectionReason()">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Rejected
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="toggleEditForm()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Hostel
                        </button>
                        <button onclick="showDeleteModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            <i class="fas fa-bed text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Rooms</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $hostel->total_rooms ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-4">
                            <i class="fas fa-door-open text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Available Rooms</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $hostel->rooms_available ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg mr-4">
                            <i class="fas fa-calendar-check text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $hostel->bookings_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg mr-4">
                            <i class="fas fa-star text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg Rating</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($hostel->reviews_avg_rating ?? 0, 1) }}/5</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Hostel Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Images Gallery with Lightbox -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Hostel Images</h3>
                            <button onclick="showImageUploadModal()" class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                                <i class="fas fa-plus mr-1"></i>Add Images
                            </button>
                        </div>
                        <div class="p-6">
                            @if($hostel->images && count($hostel->images) > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="imageGallery">
                                    @foreach($hostel->images as $index => $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image) }}"
                                                 alt="{{ $hostel->name }}"
                                                 class="w-full h-32 object-cover rounded-lg shadow-sm cursor-pointer hover:opacity-90 transition-opacity"
                                                 onclick="openLightbox('{{ Storage::url($image) }}', {{ $index }})">
                                            <button onclick="deleteImage('{{ $image }}', {{ $hostel->id }}, this)"
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-image text-4xl mb-4 text-gray-300"></i>
                                    <p>No images available</p>
                                    <button onclick="showImageUploadModal()" class="mt-2 text-indigo-600 hover:text-indigo-800">
                                        Add images to showcase this hostel
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Image Upload Modal -->
                    <div id="imageUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Upload Hostel Images</h3>
                                <form id="imageUploadForm" action="{{ route('admin.hostels.images.upload', $hostel->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition-colors">
                                            <div class="space-y-1 text-center">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>Upload files</span>
                                                        <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*" required>
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                                                <p class="text-xs text-gray-500" id="selectedFiles"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="imagePreview" class="grid grid-cols-3 gap-2 mb-4 hidden"></div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="closeImageUploadModal()"
                                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit" id="uploadButton"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                disabled>
                                            Upload Images
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Lightbox Modal -->
                    <div id="lightboxModal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50" onclick="closeLightbox()">
                        <div class="relative h-full flex items-center justify-center">
                            <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300 z-50">
                                <i class="fas fa-times"></i>
                            </button>
                            <button onclick="prevImage()" class="absolute left-4 text-white text-3xl hover:text-gray-300">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <img id="lightboxImage" src="" alt="Hostel Image" class="max-h-[90vh] max-w-[90vw] object-contain">
                            <button onclick="nextImage()" class="absolute right-4 text-white text-3xl hover:text-gray-300">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <div id="lightboxCaption" class="absolute bottom-4 left-0 right-0 text-center text-white text-sm"></div>
                        </div>
                    </div>

                    <!-- Edit Hostel Form (Hidden by Default) -->
                    <div id="editForm" class="bg-white rounded-lg shadow-sm border border-gray-200 hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Hostel Information</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.hostels.update', $hostel->id) }}" method="POST" id="editHostelForm">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Hostel Name</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $hostel->name) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                                               required>
                                        @error('name')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                        <input type="text" id="location" name="location" value="{{ old('location', $hostel->location) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('location') border-red-500 @enderror"
                                               required>
                                        @error('location')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                   <div>
    <label for="price_per_month" class="block text-sm font-medium text-gray-700 mb-1">Rent per Month (Ksh)</label>
    <input type="number" id="price_per_month" name="price_per_month" 
           value="{{ old('price_per_month', $hostel->rent_per_month) }}"
           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('price_per_month') border-red-500 @enderror"
           min="0" step="0.01" required>
    @error('price_per_month')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
                                    <div>
                                        <label for="available_rooms" class="block text-sm font-medium text-gray-700 mb-1">Available Rooms</label>
                                        <input type="number" id="available_rooms" name="available_rooms" value="{{ old('available_rooms', $hostel->rooms_available) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('available_rooms') border-red-500 @enderror"
                                               min="0" required>
                                        @error('available_rooms')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-1">Total Rooms</label>
                                        <input type="number" id="total_rooms" name="total_rooms" value="{{ old('total_rooms', $hostel->total_rooms) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('total_rooms') border-red-500 @enderror"
                                               min="0" required>
                                        @error('total_rooms')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                                        <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $hostel->contact_phone) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('contact_phone') border-red-500 @enderror"
                                               placeholder="+254 XXX XXX XXX">
                                        @error('contact_phone')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea id="description" name="description" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                                                  required>{{ old('description', $hostel->description) }}</textarea>
                                        @error('description')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="amenities" class="block text-sm font-medium text-gray-700 mb-1">Amenities (comma separated)</label>
                                        <input type="text" id="amenities" name="amenities"
                                               value="{{ old('amenities', $hostel->amenities ? (is_array($hostel->amenities) ? implode(', ', $hostel->amenities) : $hostel->amenities) : '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="WiFi, Security, Laundry, Gym...">
                                    </div>
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_available" value="1"
                                                   {{ $hostel->is_available ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">Available for Booking</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_approved" value="1"
                                                   {{ $hostel->is_approved ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">Approved</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-6 flex space-x-3">
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                        Update Hostel
                                    </button>
                                    <button type="button" onclick="toggleEditForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Hostel Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Hostel Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-4">Basic Details</h4>
                                    <dl class="space-y-3">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Hostel Name:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Location:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->location }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Description:</dt>
                                            <dd class="text-sm text-gray-900">{{ Str::limit($hostel->description, 100) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Rent per Month:</dt>
                                            <dd class="text-sm font-semibold text-green-600">Ksh {{ number_format($hostel->rent_per_month, 2) }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Created:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->created_at->format('M d, Y') }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Last Updated:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->updated_at->format('M d, Y') }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-4">Status & Contact</h4>
                                    <dl class="space-y-3">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Availability:</dt>
                                            <dd>
                                                <span class="text-xs px-2 py-1 rounded-full {{ $hostel->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $hostel->is_available ? 'Available' : 'Unavailable' }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Approval Status:</dt>
                                            <dd>
                                                <span class="text-xs px-2 py-1 rounded-full {{ $hostel->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $hostel->is_approved ? 'Approved' : 'Pending Approval' }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Contact Phone:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->contact_phone ?? 'Not provided' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Total Rooms:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->total_rooms }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Available Rooms:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->rooms_available }}</dd>
                                        </div>
                                        @if($hostel->approved_at)
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Approved At:</dt>
                                            <dd class="text-sm text-gray-900">{{ $hostel->approved_at->format('M d, Y') }}</dd>
                                        </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Amenities</h3>
                        </div>
                        <div class="p-6">
                            @if($hostel->amenities && count($hostel->amenities) > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($hostel->amenities as $amenity)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            {{ $amenity }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No amenities listed</p>
                            @endif
                        </div>
                    </div>

                    <!-- Booking Analytics -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Booking Analytics</h3>
                        </div>
                        <div class="p-6">
                            <div class="w-full h-64">
                                <canvas id="bookingChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Landlord Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Landlord Information</h3>
                        </div>
                        <div class="p-6">
                            @if($hostel->landlord)
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $hostel->landlord->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $hostel->landlord->email }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="text-gray-900">{{ $hostel->landlord->phone ?? 'Not provided' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Member since:</span>
                                        <span class="text-gray-900">{{ $hostel->landlord->created_at->format('M Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Hostels:</span>
                                        <span class="text-gray-900">{{ $hostel->landlord->hostels_count ?? $hostel->landlord->hostels->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="text-xs px-2 py-1 rounded-full {{ $hostel->landlord->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $hostel->landlord->is_active ? 'Active' : 'Suspended' }}
                                        </span>
                                    </div>
                                    @if($hostel->landlord->is_approved)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Approved:</span>
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                                            Yes
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.show', $hostel->landlord->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View Landlord Profile
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">No landlord information available</p>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <!-- Contact Landlord -->
                            @if($hostel->landlord)
                                <a href="{{ route('admin.messages') }}?user={{ $hostel->landlord->id }}"
                                        class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Contact Landlord
                                </a>
                            @endif

                            <!-- Approve Hostel -->
                            @if(!$hostel->is_approved)
                                <form action="{{ route('admin.hostels.approve', $hostel->id) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200"
                                            onclick="return confirm('Are you sure you want to approve this hostel? The landlord will be notified.')">
                                        <i class="fas fa-check mr-2"></i>
                                        Approve Hostel
                                    </button>
                                </form>
                            @else
                                <button class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg flex items-center justify-center cursor-not-allowed" disabled>
                                    <i class="fas fa-check mr-2"></i>
                                    Already Approved
                                </button>
                            @endif

                            <!-- Reject Hostel -->
                            @if(!$hostel->is_approved)
                                <button onclick="showRejectModal()"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i>
                                    Reject Hostel
                                </button>
                            @else
                                <button class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg flex items-center justify-center cursor-not-allowed" disabled>
                                    <i class="fas fa-times mr-2"></i>
                                    Reject (Already Approved)
                                </button>
                            @endif

                            <!-- View Bookings -->
                            <a href="{{ route('admin.bookings') }}?hostel={{ $hostel->id }}"
                               class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200 text-center">
                                <i class="fas fa-eye mr-2"></i>
                                View Bookings ({{ $hostel->bookings_count ?? 0 }})
                            </a>

                            <!-- Toggle Availability -->
                            @if($hostel->is_available)
                                <form action="{{ route('admin.hostels.availability', $hostel->id) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_available" value="0">
                                    <button type="submit"
                                            class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200"
                                            onclick="return confirm('Are you sure you want to mark this hostel as unavailable? This will prevent new bookings.')">
                                        <i class="fas fa-pause mr-2"></i>
                                        Mark Unavailable
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.hostels.availability', $hostel->id) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_available" value="1">
                                    <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200"
                                            onclick="return confirm('Are you sure you want to mark this hostel as available?')">
                                        <i class="fas fa-play mr-2"></i>
                                        Mark Available
                                    </button>
                                </form>
                            @endif

                            <!-- Refresh Data -->
                            <button onclick="refreshData()"
                                    class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Refresh Data
                            </button>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                        </div>
                        <div class="p-6">
                            @if($hostel->bookings && $hostel->bookings->count() > 0)
                                <div class="space-y-4">
                                    @foreach($hostel->bookings->take(5) as $booking)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $booking->user->name ?? 'Unknown User' }}</p>
                                                <p class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs px-2 py-1 rounded-full
                                                    {{ $booking->booking_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                                       ($booking->booking_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($booking->booking_status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                                    {{ ucfirst($booking->booking_status) }}
                                                </span>
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-800">
                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <a href="{{ route('admin.bookings') }}?hostel={{ $hostel->id }}" 
                                   class="block text-center text-indigo-600 hover:text-indigo-800 text-sm mt-4">
                                    View all {{ $hostel->bookings->count() }} bookings
                                </a>
                            @else
                                <p class="text-gray-500 text-center py-4">No bookings yet</p>
                            @endif
                        </div>
                    </div>

                    <!-- Revenue Summary -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Revenue Summary</h3>
                        </div>
                        <div class="p-6">
                            @php
                                $paidBookings = $hostel->bookings ? $hostel->bookings->where('payment_status', 'paid') : collect();
                                $totalRevenue = $paidBookings->sum('total_amount');
                                $monthlyRevenue = $paidBookings->where('created_at', '>=', now()->startOfMonth())->sum('total_amount');
                                $pendingPayments = $hostel->bookings ? $hostel->bookings->where('payment_status', 'pending')->sum('total_amount') : 0;
                            @endphp
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Revenue:</span>
                                    <span class="text-sm font-semibold text-green-600">
                                        Ksh {{ number_format($totalRevenue, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">This Month:</span>
                                    <span class="text-sm font-semibold text-blue-600">
                                        Ksh {{ number_format($monthlyRevenue, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pending Payments:</span>
                                    <span class="text-sm font-semibold text-yellow-600">
                                        Ksh {{ number_format($pendingPayments, 2) }}
                                    </span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Average per Booking:</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            Ksh {{ $hostel->bookings && $hostel->bookings->count() > 0 ? number_format($totalRevenue / $hostel->bookings->count(), 2) : '0.00' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Hostel Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Reject Hostel</h3>
                <form action="{{ route('admin.hostels.reject', $hostel->id) }}" method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Please provide a reason for rejecting this hostel..."
                                  required></textarea>
                        <p class="text-xs text-gray-500 mt-1">This reason will be sent to the landlord.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Reject Hostel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-trash text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Delete Hostel</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete this hostel? This action cannot be undone.
                        All associated data including bookings, reviews, and images will be permanently deleted.
                    </p>
                    <p class="text-sm font-semibold text-red-600 mb-4">
                        "{{ $hostel->name }}" - {{ $hostel->location }}
                    </p>
                </div>
                <div class="flex justify-center space-x-3">
                    <button onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <form action="{{ route('admin.hostels.destroy', $hostel->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete Hostel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    @if($hostel->rejection_reason)
    <div id="rejectionReasonModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Rejection Reason</h3>
                    <button onclick="closeRejectionReasonModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-700">{{ $hostel->rejection_reason }}</p>
                    <p class="text-xs text-gray-500 mt-2">
                        Rejected on: {{ \Carbon\Carbon::parse($hostel->rejected_at)->format('M d, Y H:i') }}
                    </p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeRejectionReasonModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-5 rounded-lg flex items-center space-x-3">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600"></div>
            <span class="text-gray-700">Processing...</span>
        </div>
    </div>

    <script>
        // Global variables for lightbox
        let currentImageIndex = 0;
        let images = [];

        // Initialize images array
        @if($hostel->images && count($hostel->images) > 0)
            images = {!! json_encode(array_map(function($image) {
                return Storage::url($image);
            }, $hostel->images)) !!};
        @endif

        // Auto-hide success/error messages
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const infoMessage = document.getElementById('infoMessage');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
            if (infoMessage) infoMessage.style.display = 'none';
        }, 5000);

        // Toggle edit form
        function toggleEditForm() {
            const editForm = document.getElementById('editForm');
            editForm.classList.toggle('hidden');
            if (!editForm.classList.contains('hidden')) {
                window.scrollTo({
                    top: editForm.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        }

        // Initialize booking chart with real data
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bookingChart').getContext('2d');
            
            // Get booking data from the last 6 months
            const months = [];
            const bookingCounts = [];
            
            @if($hostel->bookings && $hostel->bookings->count() > 0)
                @php
                    $last6Months = collect(range(5, 0))->map(function($i) {
                        return now()->subMonths($i)->format('M');
                    })->toArray();
                    
                    $monthlyBookings = collect(range(5, 0))->map(function($i) {
                        return $hostel->bookings->filter(function($booking) use ($i) {
                            return $booking->created_at->format('Y-m') === now()->subMonths($i)->format('Y-m');
                        })->count();
                    })->toArray();
                @endphp
                
                months = {!! json_encode($last6Months) !!};
                bookingCounts = {!! json_encode($monthlyBookings) !!};
            @else
                months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                bookingCounts = [0, 0, 0, 0, 0, 0];
            @endif

            const bookingChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Bookings',
                        data: bookingCounts,
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(99, 102, 241)',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            callbacks: {
                                label: function(context) {
                                    return `Bookings: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        // Lightbox functionality
        function openLightbox(imageUrl, index) {
            currentImageIndex = index;
            document.getElementById('lightboxImage').src = imageUrl;
            document.getElementById('lightboxModal').classList.remove('hidden');
            document.getElementById('lightboxCaption').innerHTML = `Image ${index + 1} of ${images.length}`;
        }

        function closeLightbox() {
            document.getElementById('lightboxModal').classList.add('hidden');
        }

        function prevImage() {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                document.getElementById('lightboxImage').src = images[currentImageIndex];
                document.getElementById('lightboxCaption').innerHTML = `Image ${currentImageIndex + 1} of ${images.length}`;
            }
        }

        function nextImage() {
            if (currentImageIndex < images.length - 1) {
                currentImageIndex++;
                document.getElementById('lightboxImage').src = images[currentImageIndex];
                document.getElementById('lightboxCaption').innerHTML = `Image ${currentImageIndex + 1} of ${images.length}`;
            }
        }

        // Keyboard navigation for lightbox
        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('lightboxModal').classList.contains('hidden')) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    prevImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                }
            }
        });

        // Image upload functionality
        function showImageUploadModal() {
            document.getElementById('imageUploadModal').classList.remove('hidden');
        }

        function closeImageUploadModal() {
            document.getElementById('imageUploadModal').classList.add('hidden');
            document.getElementById('imageUploadForm').reset();
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('selectedFiles').innerHTML = '';
            document.getElementById('uploadButton').disabled = true;
        }

        // File input change handler
        document.getElementById('images')?.addEventListener('change', function(e) {
            const files = e.target.files;
            const selectedFiles = document.getElementById('selectedFiles');
            const imagePreview = document.getElementById('imagePreview');
            const uploadButton = document.getElementById('uploadButton');
            
            if (files.length > 0) {
                selectedFiles.innerHTML = `${files.length} file(s) selected`;
                uploadButton.disabled = false;
                
                // Show image previews
                imagePreview.classList.remove('hidden');
                imagePreview.innerHTML = '';
                
                for (let i = 0; i < Math.min(files.length, 6); i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'relative';
                        preview.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg">
                            <span class="absolute -top-1 -right-1 bg-indigo-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                ${i + 1}
                            </span>
                        `;
                        imagePreview.appendChild(preview);
                    };
                    
                    reader.readAsDataURL(file);
                }
                
                if (files.length > 6) {
                    const more = document.createElement('div');
                    more.className = 'flex items-center justify-center h-20 bg-gray-100 rounded-lg text-gray-600 text-sm';
                    more.innerHTML = `+${files.length - 6} more`;
                    imagePreview.appendChild(more);
                }
            } else {
                selectedFiles.innerHTML = '';
                uploadButton.disabled = true;
                imagePreview.classList.add('hidden');
            }
        });

        // Delete image function with AJAX
        function deleteImage(imagePath, hostelId, button) {
            if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                showLoading();
                
                fetch(`/admin/hostels/${hostelId}/images`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ image_path: imagePath })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        // Remove image from DOM
                        const imageDiv = button.closest('.relative');
                        imageDiv.remove();
                        
                        // Show success message
                        showNotification('success', 'Image deleted successfully!');
                        
                        // Update images array
                        const index = images.indexOf(Storage.url(imagePath));
                        if (index > -1) images.splice(index, 1);
                        
                        // If no images left, show placeholder
                        if (document.querySelectorAll('#imageGallery .relative').length === 0) {
                            location.reload(); // Reload to show "No images" state
                        }
                    } else {
                        alert('Error deleting image: ' + data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    alert('An error occurred while deleting the image.');
                });
            }
        }

        // Reject modal functions
        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectForm').reset();
        }

        // Delete modal functions
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Rejection reason modal
        function showRejectionReason() {
            document.getElementById('rejectionReasonModal').classList.remove('hidden');
        }

        function closeRejectionReasonModal() {
            document.getElementById('rejectionReasonModal').classList.add('hidden');
        }

        // Refresh data
        function refreshData() {
            showLoading();
            location.reload();
        }

        // Loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // Notification system
        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Prevent double form submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitButton = this.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    `;
                }
            });
        });

        // Validate available rooms not greater than total rooms
        document.getElementById('available_rooms')?.addEventListener('change', function() {
            const totalRooms = parseInt(document.getElementById('total_rooms')?.value) || 0;
            const availableRooms = parseInt(this.value) || 0;
            
            if (availableRooms > totalRooms) {
                this.setCustomValidity('Available rooms cannot exceed total rooms');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('total_rooms')?.addEventListener('change', function() {
            const availableRooms = parseInt(document.getElementById('available_rooms')?.value) || 0;
            const totalRooms = parseInt(this.value) || 0;
            
            if (availableRooms > totalRooms) {
                document.getElementById('available_rooms').setCustomValidity('Available rooms cannot exceed total rooms');
                document.getElementById('available_rooms').reportValidity();
            }
        });

        // Contact landlord function
        function contactLandlord(userId) {
            window.location.href = `/admin/messages?user=${userId}`;
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = ['imageUploadModal', 'rejectModal', 'deleteModal', 'rejectionReasonModal', 'lightboxModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    if (modalId === 'lightboxModal') {
                        closeLightbox();
                    } else if (modalId === 'imageUploadModal') {
                        closeImageUploadModal();
                    } else if (modalId === 'rejectModal') {
                        closeRejectModal();
                    } else if (modalId === 'deleteModal') {
                        closeDeleteModal();
                    } else if (modalId === 'rejectionReasonModal') {
                        closeRejectionReasonModal();
                    }
                }
            });
        };
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
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        /* Modal animations */
        .fixed {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Hover effects */
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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