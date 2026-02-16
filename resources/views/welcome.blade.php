<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Hostel Management System - Find Your Perfect Accommodation</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
     <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .stat-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        /* Gradient animation for video overlay */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Ensure video covers the entire section */
.relative {
    position: relative;
}

.absolute {
    position: absolute;
}

.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

.object-cover {
    object-fit: cover;
}

/* Video container aspect ratio */
.aspect-video {
    aspect-ratio: 16 / 9;
}
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">HostelHub</span>
                    </div>
                </div>
<div class="flex items-center space-x-4">
    <a href="#features" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">Features</a>
    <a href="#how-it-works" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">How It Works</a>
    <a href="#testimonials" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">Testimonials</a>

    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">Admin Dashboard</a>
        @else
            <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">Dashboard</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 text-sm font-medium">Logout</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">Sign In</a>
        <a href="{{ route('register') }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 text-sm font-medium">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

<!-- Replace the entire Hero Section with this: -->
<section class="relative pt-16 h-screen flex items-center justify-center overflow-hidden">
    <!-- Video Background -->
    <div class="absolute inset-0 z-0">
        <video
            autoplay
            muted
            loop
            playsinline
            class="w-full h-full object-cover"
            poster="{{ asset('images/video-poster.jpg') }}"
        >
            <source src="{{ asset('videos/welcome-video.mp4') }}" type="video/mp4">
            <source src="{{ asset('videos/welcome-video.webm') }}" type="video/webm">
            <!-- Fallback image if video doesn't load -->
            <div class="hero-gradient absolute inset-0"></div>
        </video>
        <!-- Overlay for better text readability -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
            Find Your Perfect
            <span class="text-yellow-300">Student Hostel</span>
        </h1>
        <p class="text-xl mb-8 text-blue-100 leading-relaxed max-w-3xl mx-auto">
            Discover comfortable, affordable, and secure accommodations tailored for students.
            Join thousands of students who found their home away from home with us.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="bg-white text-blue-600 px-8 py-4 rounded-lg hover:bg-gray-100 font-semibold text-lg text-center transition duration-300">
                🏠 Find Your Hostel
            </a>
            <button onclick="playIntroVideo()"
               class="border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white hover:text-blue-600 font-semibold text-lg text-center transition duration-300 flex items-center justify-center">
                ▶ Watch Intro
            </button>
        </div>
    </div>
</section>


    <!-- Stats Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="stat-number text-4xl md:text-5xl font-bold mb-2">500+</div>
                    <p class="text-gray-600 font-medium">Verified Hostels</p>
                </div>
                <div>
                    <div class="stat-number text-4xl md:text-5xl font-bold mb-2">10,000+</div>
                    <p class="text-gray-600 font-medium">Happy Students</p>
                </div>
                <div>
                    <div class="stat-number text-4xl md:text-5xl font-bold mb-2">50+</div>
                    <p class="text-gray-600 font-medium">Campuses Covered</p>
                </div>
                <div>
                    <div class="stat-number text-4xl md:text-5xl font-bold mb-2">4.8/5</div>
                    <p class="text-gray-600 font-medium">Average Rating</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose HostelHub?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    We make finding and booking student accommodation simple, secure, and stress-free.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Verified & Safe</h3>
                    <p class="text-gray-600 leading-relaxed">
                        All hostels are thoroughly verified for safety, amenities, and compliance with student accommodation standards.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Affordable Pricing</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Find hostels that fit your budget with transparent pricing and no hidden fees. Various payment options available.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Easy Communication</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Direct messaging with landlords, real-time notifications, and quick response times for all your queries.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Get your perfect hostel in just a few simple steps
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Create Account</h3>
                    <p class="text-gray-600">
                        Sign up as a student and complete your profile with your institution details.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-green-600">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Search & Filter</h3>
                    <p class="text-gray-600">
                        Use advanced filters to find hostels matching your preferences and budget.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Book & Confirm</h3>
                    <p class="text-gray-600">
                        Book your preferred hostel and wait for confirmation from the landlord.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-orange-600">4</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Move In</h3>
                    <p class="text-gray-600">
                        Complete payment and move into your new home away from home!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="bg-gray-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">What Students Say</h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Hear from students who found their perfect accommodation through HostelHub
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gray-800 rounded-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 mr-2">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        "HostelHub made my transition to university so smooth! Found a great place near campus within my budget. The verification process gave me peace of mind."
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-semibold">SK</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Sarah K.</h4>
                            <p class="text-gray-400 text-sm">University of Nairobi</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-gray-800 rounded-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 mr-2">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        "The messaging system is fantastic! I could communicate directly with landlords and get quick responses. Found my hostel in just 2 days!"
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-semibold">JM</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">John M.</h4>
                            <p class="text-gray-400 text-sm">Kenyatta University</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-gray-800 rounded-2xl p-8">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 mr-2">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        "As an international student, HostelHub was a lifesaver. The verified hostels and secure payment system made everything so easy and safe."
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-semibold">AD</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Amina D.</h4>
                            <p class="text-gray-400 text-sm">International Student</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <!-- CTA Section with Background Video -->
<section class="relative py-20 overflow-hidden">
    <!-- Video Background -->
    <div class="absolute inset-0 z-0">
        <video
            autoplay
            muted
            loop
            playsinline
            class="w-full h-full object-cover"
            poster="{{ asset('images/cta-video-poster.jpg') }}"
        >
            <source src="{{ asset('videos/cta-background.mp4') }}" type="video/mp4">
            <source src="{{ asset('videos/cta-background.webm') }}" type="video/webm">
            <!-- Fallback gradient if video doesn't load -->
            <div class="hero-gradient absolute inset-0"></div>
        </video>
        <!-- Overlay for better text readability -->
        <div class="absolute inset-0 bg-blue-900 bg-opacity-70"></div>
        <!-- Optional: Animated gradient overlay for extra visual appeal -->
        <div class="absolute inset-0 opacity-30" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 50%, #667eea 100%); background-size: 400% 400%; animation: gradientShift 15s ease infinite;"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Find Your Perfect Hostel?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Join thousands of students who have found their ideal accommodation through HostelHub.
            Start your search today!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="bg-white text-blue-600 px-8 py-4 rounded-lg hover:bg-gray-100 font-semibold text-lg transition duration-300 transform hover:scale-105 shadow-lg">
                🏠 Get Started Free
            </a>
            <a href="{{ route('login') }}"
               class="border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white hover:text-blue-600 font-semibold text-lg transition duration-300 transform hover:scale-105">
                Sign In
            </a>
        </div>
        <p class="text-blue-100 mt-4">No credit card required • Free forever for students</p>

        <!-- Optional: Add a play button for sound -->
        <div class="mt-6">
            <button id="unmuteButton" class="text-white bg-black bg-opacity-30 hover:bg-opacity-50 rounded-full p-3 transition duration-300 flex items-center space-x-2 mx-auto text-sm">
                <svg id="muteIcon" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                </svg>
                <svg id="unmuteIcon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                </svg>
                <span id="unmuteText">Enable Sound</span>
            </button>
        </div>
    </div>
</section>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold">HostelHub</span>
                    </div>
                    <p class="text-gray-400">
                        Making student accommodation simple, secure, and accessible for everyone.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">For Students</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Find Hostels</a></li>
                        <li><a href="#" class="hover:text-white">How It Works</a></li>
                        <li><a href="#" class="hover:text-white">Safety Guidelines</a></li>
                        <li><a href="#" class="hover:text-white">Student Resources</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">For Landlords</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">List Your Hostel</a></li>
                        <li><a href="#" class="hover:text-white">Pricing</a></li>
                        <li><a href="#" class="hover:text-white">Hostel Guidelines</a></li>
                        <li><a href="#" class="hover:text-white">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: hello@hostelhub.com</li>
                        <li>Phone: +254 700 000 000</li>
                        <li>Nairobi, Kenya</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 HostelHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
            // Video Sound Control
    document.addEventListener('DOMContentLoaded', function() {
        const unmuteButton = document.getElementById('unmuteButton');
        const muteIcon = document.getElementById('muteIcon');
        const unmuteIcon = document.getElementById('unmuteIcon');
        const unmuteText = document.getElementById('unmuteText');
        let video = document.querySelector('.hero-gradient + video');

        if (!video) {
            // Fallback: get any video in the CTA section
            video = document.querySelector('section:has(.hero-gradient) video');
        }

        if (unmuteButton && video) {
            unmuteButton.addEventListener('click', function() {
                if (video.muted) {
                    // Unmute video
                    video.muted = false;
                    muteIcon.classList.add('hidden');
                    unmuteIcon.classList.remove('hidden');
                    unmuteText.textContent = 'Sound On';
                    unmuteButton.classList.add('bg-green-500', 'bg-opacity-50');
                    unmuteButton.classList.remove('bg-black', 'bg-opacity-30');
                } else {
                    // Mute video
                    video.muted = true;
                    muteIcon.classList.remove('hidden');
                    unmuteIcon.classList.add('hidden');
                    unmuteText.textContent = 'Enable Sound';
                    unmuteButton.classList.remove('bg-green-500', 'bg-opacity-50');
                    unmuteButton.classList.add('bg-black', 'bg-opacity-30');
                }
            });
        }
    });
        // Video modal functionality
function openVideoModal() {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    video.play();
}

function closeVideo() {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    video.pause();
    video.currentTime = 0;
}

// Inline video play function
function playVideo() {
    const video = document.getElementById('introVideo');
    const overlay = document.getElementById('videoOverlay');
    video.play();
    overlay.style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('videoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVideo();
    }
});

// Escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVideo();
    }
});
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-white', 'shadow-lg');
            } else {
                nav.classList.remove('bg-white', 'shadow-lg');
            }
        });
    </script>
</body>
</html>
