<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Service Requests - HostelHub</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('service-provider.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">HostelHub Services</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->serviceProvider->company_name }}</span>
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
                    <a href="{{ route('service-provider.dashboard') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('service-provider.requests') }}"
                       class="flex items-center px-4 py-3 text-gray-700 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Service Requests
                    </a>
                    <a href="{{ route('service-provider.profile') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Service Requests</h1>
                <p class="text-gray-600 mt-2">Manage and track all service requests from students</p>
            </div>

            <!-- Requests Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">All Service Requests</h3>
                </div>
                <div class="p-6">
                    @if($requests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Student & Service</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Location</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Urgency</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Date</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-4 px-4">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $request->student->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $request->title }}</p>
                                                    <p class="text-xs text-gray-400">{{ $request->service_type }}</p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <p class="text-sm text-gray-600">{{ $request->hostel->name }}</p>
                                                <p class="text-xs text-gray-500">Room {{ $request->room_number }}</p>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-xs px-2 py-1 rounded-full {{ $request->getUrgencyBadgeClass() }}">
                                                    {{ ucfirst($request->urgency_level) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <span class="text-xs px-2 py-1 rounded-full {{ $request->getStatusBadgeClass() }}">
                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ $request->created_at->format('M j, Y') }}
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex space-x-2">
                                                    @if($request->status === 'pending')
                                                        <form action="{{ route('service-provider.requests.accept', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                                                Accept
                                                            </button>
                                                        </form>
                                                    @elseif($request->status === 'accepted')
                                                        <form action="{{ route('service-provider.requests.start', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                                Start Job
                                                            </button>
                                                        </form>
                                                    @elseif($request->status === 'in_progress')
                                                        <form action="{{ route('service-provider.requests.complete', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="text-xs bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700">
                                                                Complete
                                                            </button>
                                                        </form>
                                                    @elseif($request->status === 'completed')
                                                        <span class="text-xs text-gray-400">Completed</span>
                                                    @endif

                                                    <!-- View Details Button -->
                                                    <button onclick="viewRequestDetails({{ $request->id }})"
                                                        class="text-xs bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">
                                                        Details
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500">No service requests found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div id="requestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Service Request Details</h3>
                    <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="requestDetails" class="space-y-4">
                    <!-- Details will be loaded here via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewRequestDetails(requestId) {
            // In a real application, you would fetch the request details via AJAX
            // For now, we'll show a simple message
            document.getElementById('requestDetails').innerHTML = `
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
                <p class="text-gray-600">Loading request details...</p>
            `;

            document.getElementById('requestModal').classList.remove('hidden');

            // Simulate loading data
            setTimeout(() => {
                document.getElementById('requestDetails').innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Student Information</h4>
                            <p class="text-sm text-gray-600" id="studentInfo">Loading...</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Service Details</h4>
                            <p class="text-sm text-gray-600" id="serviceInfo">Loading...</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Issue Description</h4>
                        <p class="text-sm text-gray-600 mt-1" id="issueDescription">Loading...</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">Location</h4>
                            <p class="text-sm text-gray-600" id="locationInfo">Loading...</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Contact Information</h4>
                            <p class="text-sm text-gray-600" id="contactInfo">Loading...</p>
                        </div>
                    </div>
                `;
            }, 500);
        }

        function closeRequestModal() {
            document.getElementById('requestModal').classList.add('hidden');
        }
    </script>
</body>
</html>
