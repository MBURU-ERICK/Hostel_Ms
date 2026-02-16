<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AdminMessageController extends AdminController
{
    /**
     * Admin messages dashboard
     */
    public function index(): View
    {
        $admin = Auth::user();

        // Get all users for messaging
        $users = User::where('id', '!=', $admin->id)
            ->whereIn('user_type', ['student', 'landlord', 'service_provider'])
            ->get()
            ->groupBy('user_type');

        // Get conversations
        $conversations = Conversation::where(function($query) use ($admin) {
                $query->where('user1_id', $admin->id)
                      ->orWhere('user2_id', $admin->id);
            })
            ->with(['user1', 'user2', 'lastMessage'])
            ->withCount(['messages as unread_count' => function($query) use ($admin) {
                $query->where('receiver_id', $admin->id)->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $selectedConversation = null;
        if (request('conversation_id')) {
            $selectedConversation = Conversation::with(['messages.sender', 'user1', 'user2'])
                ->find(request('conversation_id'));
        }

        $stats = [
            'total_users' => User::where('id', '!=', $admin->id)->count(),
            'total_students' => User::where('user_type', 'student')->count(),
            'total_landlords' => User::where('user_type', 'landlord')->count(),
            'total_service_providers' => User::where('user_type', 'service_provider')->count(),
        ];

        return view('admin.messages.index', compact(
            'users',
            'conversations',
            'selectedConversation',
            'stats'
        ));
    }

    /**
     * Send message to all users
     */
    public function sendToAll(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'user_types' => 'required|array',
            'user_types.*' => 'in:student,landlord,service_provider'
        ]);

        $admin = Auth::user();
        $users = User::whereIn('user_type', $request->user_types)
            ->where('id', '!=', $admin->id)
            ->get();

        foreach ($users as $user) {
            // Find or create conversation
            $conversation = $this->findOrCreateConversation($admin->id, $user->id);

            // Create message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $admin->id,
                'receiver_id' => $user->id,
                'message' => $request->message,
                'is_read' => false,
                'message_type' => 'broadcast'
            ]);

            // Create notification
            app(NotificationService::class)->createNotification(
                $user->id,
                'Admin Message',
                $request->message,
                'admin_message',
                route('admin.messages') . "?conversation_id={$conversation->id}"
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Message sent to {$users->count()} users"
        ]);
    }

    /**
     * Send message to specific user
     */
    public function sendToUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $admin = Auth::user();
        $user = User::findOrFail($request->user_id);

        // Find or create conversation
        $conversation = $this->findOrCreateConversation($admin->id, $user->id);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'receiver_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation
        $conversation->update(['last_message_at' => now()]);

        // Create notification
        NotificationService::createNotification(
            $user->id,
            'Admin Message',
            $request->message,
            'admin_message',
            route('admin.messages') . "?conversation_id={$conversation->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'conversation_id' => $conversation->id
        ]);
    }

    /**
     * Find or create conversation
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
    // In your AdminMessageController or MessageController
public function sendQuickMessage(Request $request)
{
    $request->validate([
        'recipient_id' => 'required|exists:users,id',
        'message' => 'required|string|max:1000'
    ]);

    try {
        $admin = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);

        // Find or create conversation
        $conversation = Conversation::where(function($query) use ($admin, $recipient) {
                $query->where('user1_id', $admin->id)->where('user2_id', $recipient->id);
            })
            ->orWhere(function($query) use ($admin, $recipient) {
                $query->where('user1_id', $recipient->id)->where('user2_id', $admin->id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $admin->id,
                'user2_id' => $recipient->id,
                'conversation_type' => 'direct',
                'last_message_at' => now()
            ]);
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'receiver_id' => $recipient->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Send notification to recipient
        NotificationService::createNotification(
            $recipient->id,
            'New Message from Admin',
            "You have a new message from {$admin->name}",
            'message',
            $this->getMessageRoute($recipient) . "?conversation_id={$conversation->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'conversation_id' => $conversation->id
        ]);

    } catch (\Exception $e) {
        \Log::error('Quick message sending failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to send message. Please try again.'
        ], 500);
    }
}

    /**
     * Get the appropriate message route for a recipient.
     */
    private function getMessageRoute(User $recipient): string
    {
        // Regular frontend user types should be directed to the user messages route.
        $frontendUserTypes = ['student', 'landlord', 'service_provider'];

        if (in_array($recipient->user_type, $frontendUserTypes, true)) {
            return route('messages');
        }

        // Fallback to admin messages for other user types (e.g. admin).
        return route('admin.messages');
    }
}
