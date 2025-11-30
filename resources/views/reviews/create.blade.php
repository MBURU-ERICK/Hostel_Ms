<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Write a Review - {{ $booking->hostel->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('student.dashboard') }}" class="flex items-center">
                            <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Review Form -->
        <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Write a Review</h1>
                        <p class="text-gray-600 mt-2">Share your experience at {{ $booking->hostel->name }}</p>
                    </div>

                    <form action="{{ route('reviews.store', $booking->id) }}" method="POST">
                        @csrf

                        <!-- Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Overall Rating</label>
                            <div class="flex space-x-1" x-data="{ rating: 0 }">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            @click="rating = {{ $i }}"
                                            @mouseenter="rating = {{ $i }}"
                                            class="text-3xl focus:outline-none"
                                            :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">
                                        ★
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" x-model="rating" required>
                            </div>
                            @error('rating')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-6">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Review
                            </label>
                            <textarea name="comment" id="comment" rows="6"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Share details of your experience at this hostel..."
                                      required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('student.view-hostel', $booking->hostel_id) }}"
                               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 font-medium">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>