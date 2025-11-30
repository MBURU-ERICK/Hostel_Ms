<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <!-- In the navigation bar, add notifications dropdown -->
<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo and other navigation items -->
            </div>

            <div class="flex items-center space-x-4">
                <!-- Notifications Dropdown -->
                @php
                    $unreadCount = \App\Services\NotificationService::getUnreadCount(Auth::id());
                    $notifications = \App\Services\NotificationService::getRecentNotifications(Auth::id(), 5);
                @endphp
                <div>
                    @include('components.notifications-dropdown', [
                        'unreadCount' => $unreadCount,
                        'notifications' => $notifications
                    ])
                </div>

                <!-- User menu -->
                <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>

                <!-- Logout -->
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

        @stack('modals')

        @livewireScripts
    </body>
</html>
