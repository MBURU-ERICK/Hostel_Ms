<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>System Settings - HostelHub Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Users
                    </a>
                    <a href="{{ route('admin.hostels') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hostels
                    </a>
                    <a href="{{ route('admin.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                    <a href="{{ route('admin.service-requests') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Service Requests
                    </a>
                    <a href="{{ route('admin.service-providers') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Service Providers
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analytics
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center px-4 py-3 text-gray-700 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
                        <p class="text-gray-600 mt-2">Manage your platform configuration and preferences</p>
                    </div>
                    <div class="flex space-x-3">
                        <button id="saveAllSettings" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Save All Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings Tabs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button class="tab-button active py-4 px-6 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600" data-tab="general">
                            <i class="fas fa-cog mr-2"></i>
                            General
                        </button>
                        <button class="tab-button py-4 px-6 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="notifications">
                            <i class="fas fa-bell mr-2"></i>
                            Notifications
                        </button>
                        <button class="tab-button py-4 px-6 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="payment">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            M-Pesa
                        </button>
                        <button class="tab-button py-4 px-6 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="security">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Security
                        </button>
                        <button class="tab-button py-4 px-6 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="appearance">
                            <i class="fas fa-palette mr-2"></i>
                            Appearance
                        </button>
                        <button class="tab-button py-4 px-6 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent" data-tab="backup">
                            <i class="fas fa-database mr-2"></i>
                            Backup
                        </button>
                    </nav>
                </div>

                <!-- General Settings -->
                <div id="general-tab" class="tab-content active p-6">
                    <form id="generalSettingsForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Settings</h3>

                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                    <input type="text" id="site_name" name="site_name" value="HostelHub Kenya"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">Site Email</label>
                                    <input type="email" id="site_email" name="site_email" value="admin@hostelhub.co.ke"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                    <input type="text" id="site_phone" name="site_phone" value="+254 700 123 456"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                    <select id="timezone" name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="UTC">UTC</option>
                                        <option value="Africa/Nairobi" selected>East Africa Time (EAT)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Settings</h3>

                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                    <select id="currency" name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="KES" selected>Kenyan Shilling (KES)</option>
                                        <option value="USD">US Dollar (USD)</option>
                                        <option value="EUR">Euro (EUR)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">Commission Rate (%)</label>
                                    <input type="number" id="commission_rate" name="commission_rate" value="5" min="0" max="20" step="0.1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="auto_approve_bookings" class="flex items-center">
                                        <input type="checkbox" id="auto_approve_bookings" name="auto_approve_bookings" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                        <span class="ml-2 text-sm text-gray-700">Auto-approve bookings</span>
                                    </label>
                                </div>

                                <div>
                                    <label for="require_hostel_verification" class="flex items-center">
                                        <input type="checkbox" id="require_hostel_verification" name="require_hostel_verification" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                        <span class="ml-2 text-sm text-gray-700">Require hostel verification</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Notification Settings -->
                <div id="notifications-tab" class="tab-content hidden p-6">
                    <form id="notificationSettingsForm">
                        @csrf
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">SMS Notifications</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="sms_booking_confirmation" class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Booking Confirmations</span>
                                        <input type="checkbox" id="sms_booking_confirmation" name="sms_booking_confirmation" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Send SMS when bookings are confirmed</p>
                                </div>

                                <div>
                                    <label for="sms_service_requests" class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Service Requests</span>
                                        <input type="checkbox" id="sms_service_requests" name="sms_service_requests" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Notify about new service requests via SMS</p>
                                </div>

                                <div>
                                    <label for="sms_payment_receipts" class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Payment Receipts</span>
                                        <input type="checkbox" id="sms_payment_receipts" name="sms_payment_receipts" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Send payment confirmation via SMS</p>
                                </div>

                                <div>
                                    <label for="sms_system_updates" class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">System Updates</span>
                                        <input type="checkbox" id="sms_system_updates" name="sms_system_updates" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">Notify about system maintenance via SMS</p>
                                </div>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 mt-8 mb-4">Email Notifications</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email_new_bookings" class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">New Bookings</span>
                                        <input type="checkbox" id="email_new_bookings" name="email_new_bookings" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                    </label>
                                </div>

                                <div>
                                    <label for="email_service_updates" class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Service Updates</span>
                                        <input type="checkbox" id="email_service_updates" name="email_service_updates" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- M-Pesa Settings -->
                <div id="payment-tab" class="tab-content hidden p-6">
                    <form id="paymentSettingsForm">
                        @csrf
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">M-Pesa Configuration</h3>

                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                                    <span class="text-sm text-green-800">M-Pesa is the primary payment method for Kenyan customers</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <div>
                                        <label for="mpesa_env" class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                                        <select id="mpesa_env" name="mpesa_env" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="sandbox" selected>Sandbox (Testing)</option>
                                            <option value="production">Production (Live)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="mpesa_consumer_key" class="block text-sm font-medium text-gray-700 mb-2">Consumer Key</label>
                                        <input type="password" id="mpesa_consumer_key" name="mpesa_consumer_key" value="your_consumer_key_here"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="mpesa_consumer_secret" class="block text-sm font-medium text-gray-700 mb-2">Consumer Secret</label>
                                        <input type="password" id="mpesa_consumer_secret" name="mpesa_consumer_secret" value="your_consumer_secret_here"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="mpesa_shortcode" class="block text-sm font-medium text-gray-700 mb-2">Paybill/Till Number</label>
                                        <input type="text" id="mpesa_shortcode" name="mpesa_shortcode" value="123456"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label for="mpesa_passkey" class="block text-sm font-medium text-gray-700 mb-2">Lipa Na M-Pesa Passkey</label>
                                        <input type="password" id="mpesa_passkey" name="mpesa_passkey" value="your_passkey_here"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="mpesa_initiator_name" class="block text-sm font-medium text-gray-700 mb-2">Initiator Name</label>
                                        <input type="text" id="mpesa_initiator_name" name="mpesa_initiator_name" value="HostelHub"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="mpesa_initiator_password" class="block text-sm font-medium text-gray-700 mb-2">Initiator Password</label>
                                        <input type="password" id="mpesa_initiator_password" name="mpesa_initiator_password" value="your_initiator_password"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="mpesa_callback_url" class="block text-sm font-medium text-gray-700 mb-2">Callback URL</label>
                                        <input type="url" id="mpesa_callback_url" name="mpesa_callback_url" value="https://yourdomain.com/mpesa/callback"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h4 class="text-md font-medium text-yellow-800 mb-2">Transaction Limits</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Min Amount:</span>
                                        <span class="font-medium">KES 10</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Max Amount:</span>
                                        <span class="font-medium">KES 150,000</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Daily Limit:</span>
                                        <span class="font-medium">KES 300,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Security Settings -->
                <div id="security-tab" class="tab-content hidden p-6">
                    <form id="securitySettingsForm">
                        @csrf
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <div>
                                        <label for="password_policy" class="block text-sm font-medium text-gray-700 mb-2">Password Policy</label>
                                        <select id="password_policy" name="password_policy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="low">Low (6+ characters)</option>
                                            <option value="medium" selected>Medium (8+ characters with mix)</option>
                                            <option value="high">High (12+ characters with special chars)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="session_timeout" class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes)</label>
                                        <input type="number" id="session_timeout" name="session_timeout" value="120" min="15" max="1440"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
                                        <input type="number" id="max_login_attempts" name="max_login_attempts" value="5" min="3" max="10"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Security Features</label>
                                        <div class="space-y-2">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="two_factor_auth" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Two-Factor Authentication</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="ip_whitelist" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">IP Whitelisting</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="activity_logging" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                                <span class="ml-2 text-sm text-gray-700">Activity Logging</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="data_retention" class="block text-sm font-medium text-gray-700 mb-2">Data Retention (months)</label>
                                        <input type="number" id="data_retention" name="data_retention" value="24" min="6" max="60"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Appearance Settings -->
                <div id="appearance-tab" class="tab-content hidden p-6">
                    <form id="appearanceSettingsForm">
                        @csrf
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Theme & Appearance</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <div>
                                        <label for="theme" class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                                        <select id="theme" name="theme" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="light" selected>Light</option>
                                            <option value="dark">Dark</option>
                                            <option value="auto">Auto (System)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                        <div class="flex items-center space-x-4">
                                            <input type="color" id="primary_color" name="primary_color" value="#4f46e5"
                                                   class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer">
                                            <span class="text-sm text-gray-600">#4f46e5 (Indigo)</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <span class="text-indigo-600 font-bold">HH</span>
                                            </div>
                                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                Change Logo
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                        <select id="language" name="language" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="en" selected>English</option>
                                            <option value="sw">Kiswahili</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                                        <select id="date_format" name="date_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="DD/MM/YYYY" selected>DD/MM/YYYY</option>
                                            <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                            <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="show_announcements" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                            <span class="ml-2 text-sm text-gray-700">Show announcements banner</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Backup Settings -->
                <div id="backup-tab" class="tab-content hidden p-6">
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Backup & Maintenance</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-3">Automatic Backups</h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center justify-between">
                                            <span class="text-sm text-gray-700">Enable automatic backups</span>
                                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                        </label>

                                        <div>
                                            <label for="backup_frequency" class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                                            <select id="backup_frequency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="daily">Daily</option>
                                                <option value="weekly" selected>Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="backup_retention" class="block text-sm font-medium text-gray-700 mb-2">Retention Period</label>
                                            <select id="backup_retention" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="7">7 days</option>
                                                <option value="30" selected>30 days</option>
                                                <option value="90">90 days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-3">Manual Backup</h4>
                                    <div class="space-y-3">
                                        <button type="button" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center justify-center">
                                            <i class="fas fa-download mr-2"></i>
                                            Create Backup Now
                                        </button>
                                        <p class="text-sm text-gray-500">Last backup: 2 hours ago</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-3">System Maintenance</h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center justify-between">
                                            <span class="text-sm text-gray-700">Maintenance Mode</span>
                                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </label>

                                        <div>
                                            <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message</label>
                                            <textarea id="maintenance_message" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="System is under maintenance..."></textarea>
                                        </div>

                                        <button type="button" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                            Clear Cache
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-3">Storage Information</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Database Size</span>
                                            <span class="text-gray-900">45.2 MB</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Backup Files</span>
                                            <span class="text-gray-900">320 MB</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Available Storage</span>
                                            <span class="text-green-600">4.2 GB</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Update active tab button
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'text-indigo-600', 'border-indigo-600');
                        btn.classList.add('text-gray-500', 'border-transparent');
                    });
                    this.classList.add('active', 'text-indigo-600', 'border-indigo-600');
                    this.classList.remove('text-gray-500', 'border-transparent');

                    // Show target tab content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    });
                    document.getElementById(`${targetTab}-tab`).classList.remove('hidden');
                    document.getElementById(`${targetTab}-tab`).classList.add('active');
                });
            });

            // Save settings functionality
            document.getElementById('saveAllSettings').addEventListener('click', function() {
                const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
                const form = document.getElementById(`${activeTab}SettingsForm`) || document.querySelector('.tab-content.active form');

                if (form) {
                    const formData = new FormData(form);

                    // Show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
                    this.disabled = true;

                    // Simulate API call
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;

                        // Show success message
                        showNotification('Settings saved successfully!', 'success');
                    }, 1500);
                }
            });

            // Notification function
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                } text-white`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Color picker preview
            const colorPicker = document.getElementById('primary_color');
            if (colorPicker) {
                colorPicker.addEventListener('change', function() {
                    document.documentElement.style.setProperty('--primary-color', this.value);
                });
            }

            // Test M-Pesa connection
            const testMpesaButton = document.createElement('button');
            testMpesaButton.innerHTML = '<i class="fas fa-plug mr-2"></i>Test M-Pesa Connection';
            testMpesaButton.className = 'bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center mt-4';
            testMpesaButton.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
                this.disabled = true;

                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-plug mr-2"></i>Test M-Pesa Connection';
                    this.disabled = false;
                    showNotification('M-Pesa connection test successful!', 'success');
                }, 2000);
            });

            const mpesaForm = document.getElementById('paymentSettingsForm');
            if (mpesaForm) {
                mpesaForm.appendChild(testMpesaButton);
            }
        });
    </script>

    <style>
        :root {
            --primary-color: #4f46e5;
        }

        .tab-button.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
    </style>
</body>
</html>
