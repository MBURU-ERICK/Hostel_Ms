<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Booking;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    /**
     * Display messages for a booking (Legacy method for existing routes)
     */
    public function index($bookingId): View
    {
        // Check if user is student or landlord and redirect accordingly
        $user = Auth::user();

        if ($user->isStudent()) {
            return $this->studentBookingMessages($bookingId);
        } elseif ($user->isLandlord()) {
            return $this->landlordBookingMessages($bookingId);
        } else {
            abort(403, 'Unauthorized access to messages.');
        }
    }

    /**
     * Student messages for a specific booking (legacy support)
     */
private function studentBookingMessages($bookingId): View
{
    // Get the booking with proper relationships
    $booking = Booking::with(['hostel.landlord', 'messages.sender'])
                     ->findOrFail($bookingId);

    // Check if user is authorized to view these messages
    if (Auth::id() !== $booking->user_id) {
        abort(403, 'Unauthorized access to messages.');
    }

    // Mark messages as read when viewing
    Message::where('booking_id', $bookingId)
           ->where('receiver_id', Auth::id())
           ->where('is_read', false)
           ->update(['is_read' => true]);

    // Get all conversations for the sidebar using the new method
    $conversations = $this->getStudentConversationsForView();

    return view('student.messages.index', compact('booking', 'conversations'));
}

    /**
     * Get conversation messages for student - NEW METHOD
     */
    public function getStudentConversationMessages(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        // Check if user is part of the conversation
        if ($conversation->user1_id != $user->id && $conversation->user2_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $otherUser = $conversation->user1_id == $user->id ? $conversation->user2 : $conversation->user1;

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'otherUser' => $otherUser
        ]);
    }

    /**
     * Get landlords for student - NEW METHOD
     */
    public function getStudentLandlords(): JsonResponse
    {
        $student = Auth::user();

        $landlords = User::where('user_type', 'landlord')
            ->whereHas('hostels.bookings', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->with(['hostels' => function($query) use ($student) {
                $query->whereHas('bookings', function($q) use ($student) {
                    $q->where('user_id', $student->id);
                });
            }])
            ->get()
            ->map(function($landlord) {
                return [
                    'id' => $landlord->id,
                    'name' => $landlord->name,
                    'email' => $landlord->email,
                    'hostel_name' => $landlord->hostels->first()->name ?? 'No hostel',
                ];
            });

        return response()->json([
            'success' => true,
            'landlords' => $landlords
        ]);
    }

    /**
     * Get service providers for student - NEW METHOD
     */
    public function getStudentServiceProviders(): JsonResponse
    {
        $serviceProviders = User::where('user_type', 'service_provider')
            ->whereHas('serviceProviderDetail', function($query) {
                $query->where('is_verified', true)
                      ->where('is_available', true);
            })
            ->with('serviceProviderDetail')
            ->get()
            ->map(function($user) {
                $provider = $user->serviceProviderDetail;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'company_name' => $provider->company_name ?? $user->name,
                    'service_type' => $provider->service_type ?? 'general',
                    'service_type_name' => $provider->service_type ?
                        ucfirst(str_replace('_', ' ', $provider->service_type)) : 'General Services',
                    'rating' => $provider->rating ?? 0,
                    'experience_years' => $provider->experience_years ?? 0,
                ];
            });

        return response()->json([
            'success' => true,
            'service_providers' => $serviceProviders
        ]);
    }

    /**
     * Create service request for student - NEW METHOD
     */
    public function createStudentServiceRequest(Request $request): JsonResponse
    {
        $request->validate([
            'service_type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'urgency' => 'required|in:low,medium,high,emergency'
        ]);

        try {
            $student = Auth::user();

            $serviceRequest = ServiceRequest::create([
                'user_id' => $student->id,
                'service_type' => $request->service_type,
                'description' => $request->description,
                'urgency' => $request->urgency,
                'status' => 'pending',
            ]);

            // Find available service providers for this service type
            $serviceProviders = User::where('user_type', 'service_provider')
                ->whereHas('serviceProviderDetail', function($query) use ($request) {
                    $query->where('is_verified', true)
                          ->where('is_available', true)
                          ->where('service_type', $request->service_type);
                })
                ->get();

            // Create conversations with service providers
            foreach ($serviceProviders as $provider) {
                $conversation = $this->findOrCreateConversation($student->id, $provider->id);

                // Add service request info to conversation
                $conversation->update([
                    'service_request_id' => $serviceRequest->id
                ]);

                // Send initial message
                $initialMessage = "New service request: {$request->service_type}\n\nDescription: {$request->description}\nUrgency: {$request->urgency}";

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $student->id,
                    'receiver_id' => $provider->id,
                    'message' => $initialMessage,
                    'is_read' => false,
                ]);

                $conversation->update(['last_message_at' => now()]);

                // Create notification for service provider
                NotificationService::createNotification(
                    $provider->id,
                    'New Service Request',
                    "You have a new {$request->service_type} service request",
                    'service_request',
                    route('service-provider.messages') . "?conversation_id={$conversation->id}"
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Service request submitted successfully! Service providers will contact you soon.',
                'service_request_id' => $serviceRequest->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start conversation for student - NEW METHOD
     */
    public function startStudentConversation(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $student = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Verify receiver is landlord or service provider
        if (!in_array($receiver->user_type, ['landlord', 'service_provider'])) {
            return response()->json([
                'success' => false,
                'message' => 'Can only start conversations with landlords or service providers'
            ], 400);
        }

        // Check if conversation already exists
        $conversation = $this->findOrCreateConversation($student->id, $receiver->id);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $student->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Create notification
        NotificationService::createNotification(
            $receiver->id,
            'New Message',
            "You have a new message from {$student->name}",
            'message',
            $this->getMessageRoute($receiver) . "?conversation_id={$conversation->id}"
        );

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'message' => 'Conversation started successfully'
        ]);
    }
private function getStudentConversationsForView()
{
    $user = Auth::user();

    // Get all conversations for the student
    $conversations = Conversation::where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->with(['user1', 'user2', 'lastMessage'])
        ->withCount(['messages as unread_count' => function($query) use ($user) {
            $query->where('receiver_id', $user->id)->where('is_read', false);
        }])
        ->orderBy('last_message_at', 'desc')
        ->get()
        ->map(function($conversation) use ($user) {
            $otherUser = $conversation->user1_id == $user->id ? $conversation->user2 : $conversation->user1;

            return [
                'id' => $conversation->id,
                'type' => $otherUser->user_type,
                'other_user' => $otherUser,
                'last_message' => $conversation->lastMessage ? $conversation->lastMessage->message : 'No messages yet',
                'last_message_time' => $conversation->lastMessage ? $conversation->lastMessage->created_at : $conversation->created_at,
                'unread_count' => $conversation->unread_count,
                'booking_id' => $conversation->booking_id,
            ];
        });

    return $conversations;
}
    /**
     * Landlord messages for a specific booking (legacy support)
     */
    private function landlordBookingMessages($bookingId): View
    {
        $booking = Booking::with(['user', 'hostel', 'messages.sender'])
                         ->findOrFail($bookingId);

        // Check if user is authorized to view these messages
        if (Auth::id() !== $booking->hostel->landlord_id) {
            abort(403, 'Unauthorized access to messages.');
        }

        // Mark messages as read when viewing
        Message::where('booking_id', $bookingId)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return $this->landlordMessages();
    }

    /**
     * Get student conversations for sidebar
     */
    private function getStudentConversations()
    {
        $user = Auth::user();

        // Get booking conversations
        $bookingConversations = Booking::with(['hostel', 'messages' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->where('user_id', $user->id)
            ->whereHas('messages')
            ->get()
            ->map(function($booking) use ($user) {
                $unreadCount = $booking->messages()
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $lastMessage = $booking->messages->first();

                return [
                    'id' => $booking->id,
                    'type' => 'booking',
                    'title' => $booking->hostel->name,
                    'subtitle' => $booking->hostel->location,
                    'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : $booking->created_at->diffForHumans(),
                    'unread_count' => $unreadCount,
                    'booking_status' => $booking->booking_status,
                ];
            });

        // Get service request conversations
        $serviceConversations = $user->serviceRequests()
            ->with(['serviceProviderUser', 'messages' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->whereHas('messages')
            ->get()
            ->map(function($serviceRequest) use ($user) {
                $unreadCount = Message::whereHas('conversation', function($query) use ($serviceRequest) {
                    $query->where('service_request_id', $serviceRequest->id);
                })
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

                $lastMessage = $serviceRequest->messages->first();

                return [
                    'id' => $serviceRequest->id,
                    'type' => 'service_provider',
                    'title' => $serviceRequest->serviceProviderUser->name ?? 'Service Provider',
                    'subtitle' => 'Service Request',
                    'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : $serviceRequest->created_at->diffForHumans(),
                    'unread_count' => $unreadCount,
                    'service_status' => $serviceRequest->status,
                ];
            });

        return $bookingConversations->merge($serviceConversations);
    }

    /**
     * Student messages dashboard - NEW METHOD
     */
    public function studentMessages(): View
    {
        $user = Auth::user();

        // Get all conversations for the student
        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->with(['user1', 'user2', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('receiver_id', $user->id)->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) use ($user) {
                $otherUser = $conversation->user1_id == $user->id ? $conversation->user2 : $conversation->user1;

                return [
                    'id' => $conversation->id,
                    'type' => $otherUser->user_type,
                    'other_user' => $otherUser,
                    'last_message' => $conversation->lastMessage ? $conversation->lastMessage->message : 'No messages yet',
                    'last_message_time' => $conversation->lastMessage ? $conversation->lastMessage->created_at : $conversation->created_at,
                    'unread_count' => $conversation->unread_count,
                    'booking_id' => $conversation->booking_id,
                ];
            });

        $selectedConversation = null;
        if (request('conversation_id')) {
            $selectedConversation = Conversation::with(['messages.sender', 'user1', 'user2'])
                ->find(request('conversation_id'));

            if ($selectedConversation &&
                ($selectedConversation->user1_id == $user->id || $selectedConversation->user2_id == $user->id)) {

                // Mark messages as read
                Message::where('conversation_id', $selectedConversation->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            } else {
                $selectedConversation = null;
            }
        }

        $stats = [
            'total_conversations' => $conversations->count(),
            'unread_messages' => $conversations->sum('unread_count'),
            'landlords_count' => $conversations->where('type', 'landlord')->count(),
            'service_providers_count' => $conversations->where('type', 'service_provider')->count(),
        ];

        return view('student.messages.index', compact('conversations', 'selectedConversation', 'stats'));
    }

    /**
     * Landlord messages dashboard
     */
    public function landlordMessages(): View
    {
        $user = Auth::user();

        // Get conversations with students and service providers
        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->with(['user1', 'user2', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('receiver_id', $user->id)->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $selectedConversation = null;
        if (request('conversation_id')) {
            $selectedConversation = Conversation::with(['messages.sender', 'user1', 'user2'])
                ->find(request('conversation_id'));

            if ($selectedConversation) {
                // Mark messages as read
                Message::where('conversation_id', $selectedConversation->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        $stats = [
            'total_messages' => Message::where('receiver_id', $user->id)->count(),
            'unread_messages' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
            'active_conversations' => $conversations->count(),
        ];

        return view('landlord.messages.index', compact('conversations', 'selectedConversation', 'stats'));
    }

    /**
 * Service Provider messages dashboard
 */
public function serviceProviderMessages(): View
{
    $user = Auth::user();

    // Get the service provider details
    $serviceProvider = $user->serviceProviderDetail;

    // Get all clients (students and landlords)
    $clients = User::whereIn('user_type', ['student', 'landlord'])
        ->where('id', '!=', $user->id)
        ->get();

    $conversations = Conversation::where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->with(['user1', 'user2', 'lastMessage'])
        ->withCount(['messages as unread_count' => function($query) use ($user) {
            $query->where('receiver_id', $user->id)->where('is_read', false);
        }])
        ->orderBy('last_message_at', 'desc')
        ->get();

    $selectedConversation = null;
    if (request('conversation_id')) {
        $selectedConversation = Conversation::with(['messages.sender', 'user1', 'user2'])
            ->find(request('conversation_id'));

 if ($selectedConversation) {
    // Mark messages as read
    Message::where('conversation_id', $selectedConversation->id)
        ->where('receiver_id', $user->id)
        ->where('is_read', false)
        ->update(['is_read' => true]);
}
    }

    $unreadCount = Message::where('receiver_id', $user->id)
        ->where('is_read', false)
        ->count();

    return view('service-provider.messages', compact(
        'conversations',
        'selectedConversation',
        'clients',
        'unreadCount',
        'serviceProvider' // Add this line to pass serviceProvider to the view
    ));
}

    /**
     * Send a message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
            'conversation_id' => 'nullable|exists:conversations,id'
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Find or create conversation
        $conversation = $request->conversation_id
            ? Conversation::find($request->conversation_id)
            : $this->findOrCreateConversation($sender->id, $receiver->id);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Create notification
        NotificationService::createNotification(
            $receiver->id,
            'New Message',
            "You have a new message from {$sender->name}",
            'message',
            $this->getMessageRoute($receiver) . "?conversation_id={$conversation->id}"
        );

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
            'conversation_id' => $conversation->id
        ]);
    }

    /**
     * Start a new conversation
     */
    public function startConversation(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);

        // Check if conversation already exists
        $conversation = $this->findOrCreateConversation($sender->id, $receiver->id);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Create notification
        NotificationService::createNotification(
            $receiver->id,
            'New Message',
            "You have a new message from {$sender->name}",
            'message',
            $this->getMessageRoute($receiver) . "?conversation_id={$conversation->id}"
        );

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'message' => 'Message sent successfully'
        ]);
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        // Check if user is part of the conversation
        if ($conversation->user1_id != $user->id && $conversation->user2_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'otherUser' => $conversation->other_user
        ]);
    }

    /**
     * Get messages for a booking (AJAX endpoint - legacy support)
     */
    public function getMessages($bookingId): JsonResponse
    {
        $booking = Booking::findOrFail($bookingId);

        // Check if user is authorized to view these messages
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->hostel->landlord_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::with('sender')
                          ->where('booking_id', $bookingId)
                          ->orderBy('created_at', 'asc')
                          ->get();

        return response()->json($messages);
    }

    /**
     * Get all conversations for sidebar (AJAX endpoint)
     */
    public function getConversations(): JsonResponse
    {
        $user = Auth::user();

        if ($user->user_type === 'student') {
            $conversations = Booking::with(['hostel', 'messages' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->where('user_id', $user->id)
                ->whereHas('messages')
                ->get()
                ->map(function($booking) use ($user) {
                    $unreadCount = $booking->messages()
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    $lastMessage = $booking->messages->first();

                    return [
                        'id' => $booking->id,
                        'type' => 'booking',
                        'title' => $booking->hostel->name,
                        'subtitle' => $booking->hostel->location,
                        'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet',
                        'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : $booking->created_at->diffForHumans(),
                        'unread_count' => $unreadCount,
                        'booking_status' => $booking->booking_status,
                    ];
                });
        } else {
            // Landlord conversations
            $conversations = Booking::whereHas('hostel', function($query) use ($user) {
                    $query->where('landlord_id', $user->id);
                })
                ->with(['user', 'hostel', 'messages' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->whereHas('messages')
                ->get()
                ->map(function($booking) use ($user) {
                    $unreadCount = Message::where('booking_id', $booking->id)
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    $lastMessage = $booking->messages->first();

                    return [
                        'id' => $booking->id,
                        'type' => 'booking',
                        'title' => $booking->user->name,
                        'subtitle' => $booking->hostel->name,
                        'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet',
                        'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : $booking->created_at->diffForHumans(),
                        'unread_count' => $unreadCount,
                        'booking_status' => $booking->booking_status,
                    ];
                });
        }

        return response()->json(['conversations' => $conversations]);
    }



    /**
     * Get message statistics (AJAX endpoint)
     */
    public function getMessageStats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_messages' => Message::where('receiver_id', $user->id)->count(),
            'unread_messages' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
            'sent_messages' => Message::where('sender_id', $user->id)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Find or create conversation between two users
     */
    private function findOrCreateConversation($user1Id, $user2Id): Conversation
    {
        $conversation = Conversation::where(function($query) use ($user1Id, $user2Id) {
                $query->where('user1_id', $user1Id)->where('user2_id', $user2Id);
            })
            ->orWhere(function($query) use ($user1Id, $user2Id) {
                $query->where('user1_id', $user2Id)->where('user2_id', $user1Id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
                'conversation_type' => 'direct',
                'last_message_at' => now()
            ]);
        }

        return $conversation;
    }

    /**
     * Get appropriate message route based on user type
     */
    private function getMessageRoute(User $user): string
    {
        return match($user->user_type) {
            'student' => route('student.messages'),
            'landlord' => route('landlord.messages'),
            'service_provider' => route('service-provider.messages'),
            default => route('dashboard')
        };
    }

    /**
     * Store a new message (legacy method)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_type' => 'required|in:student,service_provider',
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'subject' => 'nullable|string|max:255'
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->recipient_id);

        // Verify recipient type matches
        if ($request->recipient_type !== $receiver->user_type) {
            return back()->withErrors(['recipient_id' => 'Selected recipient type does not match.'])->withInput();
        }

        // Find or create conversation
        $conversation = $this->findOrCreateConversation($sender->id, $receiver->id);

        // Create message
        $messageContent = $request->message;
        if ($request->subject) {
            $messageContent = "Subject: {$request->subject}\n\n{$request->message}";
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $messageContent,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Create notification
        NotificationService::createNotification(
            $receiver->id,
            'New Message',
            "You have a new message from {$sender->name}",
            'message',
            $this->getMessageRoute($receiver) . "?conversation_id={$conversation->id}"
        );

        return redirect()->route('landlord.messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Message sent successfully!');
    }

    /**
 * Get students for landlord (students who booked landlord's hostels)
 */
public function getStudents(): JsonResponse
{
    $landlord = Auth::user();

    $students = User::whereHas('bookings', function($query) use ($landlord) {
            $query->whereHas('hostel', function($q) use ($landlord) {
                $q->where('landlord_id', $landlord->id);
            });
        })
        ->withCount(['bookings as booked_hostels_count' => function($query) use ($landlord) {
            $query->whereHas('hostel', function($q) use ($landlord) {
                $q->where('landlord_id', $landlord->id);
            });
        }])
        ->get()
        ->map(function($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'booked_hostels' => $student->booked_hostels_count . ' hostels',
            ];
        });

    return response()->json([
        'success' => true,
        'students' => $students
    ]);
}
/**
 * Get available service providers for landlord
 */
public function getServiceProviders(): JsonResponse
{
    try {
        Log::info('Fetching service providers for landlord', ['landlord_id' => Auth::id()]);
        
        // Get all verified and available service providers
        $serviceProviders = User::where('user_type', 'service_provider')
            ->whereHas('serviceProvider', function($query) {
                $query->where('is_verified', true)
                      ->where('is_available', true);
            })
            ->with('serviceProvider')
            ->get();
        
        Log::info('Found service providers', ['count' => $serviceProviders->count()]);
        
        $formattedProviders = $serviceProviders->map(function($user) {
            $provider = $user->serviceProvider;
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_name' => $provider->company_name ?? $user->name,
                'service_type' => $provider->service_type ?? 'general',
                'service_type_name' => $provider->service_type ? 
                    $provider->getServiceTypeNameAttribute() : 'General Services',
                'rating' => $provider->rating ?? 0,
                'experience_years' => $provider->experience_years ?? 0,
                'hourly_rate' => $provider->hourly_rate ?? 0,
            ];
        });
        
        return response()->json([
            'success' => true,
            'service_providers' => $formattedProviders
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error in getServiceProviders', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error loading service providers',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function create(): View
    {
        return view('landlord.messages-send');
    }

    /**
     * Store a new message from standalone form
     */

    /**
     * Send a message within an existing conversation
     */
 /**
 * Send a message within an existing conversation
 */
public function sendMessageInConversation(Request $request, Conversation $conversation): JsonResponse
{
    try {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $sender = Auth::user();

        // Verify user is part of the conversation
        if ($conversation->user1_id != $sender->id && $conversation->user2_id != $sender->id) {
            return response()->json([
                'success' => false, 
                'error' => 'Unauthorized'
            ], 403);
        }

        // Determine receiver
        $receiverId = $conversation->user1_id == $sender->id ? $conversation->user2_id : $conversation->user1_id;
        $receiver = User::findOrFail($receiverId);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Create notification
        NotificationService::createNotification(
            $receiver->id,
            'New Message',
            "You have a new message from {$sender->name}",
            'message',
            $this->getMessageRoute($receiver) . "?conversation_id={$conversation->id}"
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at,
                'sender_id' => $message->sender_id
            ],
            'conversation_id' => $conversation->id
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error sending message', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send message: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Mark messages as read (AJAX endpoint)
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        Message::where('booking_id', $request->booking_id)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
    /**
     * Show a specific conversation
     */
    public function showConversation(Conversation $conversation): View
    {
        $user = Auth::user();

        // Verify user is part of the conversation
        if ($conversation->user1_id != $user->id && $conversation->user2_id != $user->id) {
            abort(403, 'Unauthorized access to conversation.');
        }

        // Mark messages as read when viewing
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get all conversations for sidebar
        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->with(['user1', 'user2', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($query) use ($user) {
                $query->where('receiver_id', $user->id)->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $stats = [
            'total_messages' => Message::where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->count(),
            'unread_messages' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
            'active_conversations' => $conversations->count(),
        ];

        return view('landlord.messages.index', [
            'conversations' => $conversations,
            'selectedConversation' => $conversation->load(['messages.sender', 'user1', 'user2']),
            'stats' => $stats
        ]);
    }

    /**
     * Delete a conversation
     */
    public function deleteConversation(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        // Verify user is part of the conversation
        if ($conversation->user1_id != $user->id && $conversation->user2_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete all messages in the conversation
        Message::where('conversation_id', $conversation->id)->delete();

        // Delete the conversation
        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully'
        ]);
    }

    /**
     * Archive a conversation
     */
    public function archiveConversation(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        // Verify user is part of the conversation
        if ($conversation->user1_id != $user->id && $conversation->user2_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update conversation type to archived
        $conversation->update(['conversation_type' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Conversation archived successfully'
        ]);
    }

    /**
     * Get message statistics for landlord
     */
    public function getLandlordMessageStats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_messages' => Message::where('receiver_id', $user->id)->orWhere('sender_id', $user->id)->count(),
            'unread_messages' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
            'sent_messages' => Message::where('sender_id', $user->id)->count(),
            'active_conversations' => Conversation::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })->count(),
        ];

        return response()->json($stats);
    }
}
