<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index($bookingId)
    {
        // Use the correct relationships - 'hostel.landlord' instead of 'hostel.user'
        $booking = Booking::with(['hostel.landlord', 'messages.sender'])
                         ->findOrFail($bookingId);

        // Check if user is authorized to view these messages
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->hostel->landlord_id) {
            abort(403, 'Unauthorized access to messages.');
        }

        // Mark messages as read when viewing
        Message::where('booking_id', $bookingId)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return view('student.messages.index', compact('booking'));
    }

    public function store(Request $request)
{
    \Log::info('Store method called', $request->all());

    $request->validate([
        'message' => 'required|string|max:1000',
        'booking_id' => 'required|exists:bookings,id'
    ]);

    $booking = Booking::with('hostel.landlord')->findOrFail($request->booking_id);

    // Check if user is authorized to send messages for this booking
    if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->hostel->landlord_id) {
        abort(403, 'Unauthorized to send messages.');
    }

    $message = Message::create([
        'booking_id' => $request->booking_id,
        'sender_id' => Auth::id(),
        'receiver_id' => Auth::id() === $booking->user_id
                       ? $booking->hostel->landlord_id
                       : $booking->user_id,
        'message' => $request->message,
    ]);

    // Create notification for the receiver
    $senderName = Auth::user()->name;
    $receiverId = $message->receiver_id;

    NotificationService::createNotification(
        $receiverId,
        'New Message',
        "You have a new message from {$senderName} regarding booking #{$request->booking_id}",
        'message',
        route('landlord.messages', ['booking_id' => $request->booking_id])
    );

    // For AJAX requests, return JSON
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    // For regular form submissions, redirect back
    return redirect()->route('landlord.messages', ['booking_id' => $request->booking_id])
        ->with('success', 'Message sent successfully');
}
    public function getMessages($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Check if user is authorized to view these messages
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->hostel->landlord_id) {
            abort(403);
        }

        $messages = Message::with('sender')
                          ->where('booking_id', $bookingId)
                          ->orderBy('created_at', 'asc')
                          ->get();

        return response()->json($messages);
    }

    public function getUnreadCount()
    {
        $unreadCount = Message::where('receiver_id', Auth::id())
                             ->where('is_read', false)
                             ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
    public function landlordMessages()
    {
        // Get all bookings for this landlord that have messages
        $bookings = Booking::whereHas('hostel', function($query) {
                $query->where('landlord_id', Auth::id());
            })
            ->with(['user', 'hostel', 'messages' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->whereHas('messages') // Only show bookings with messages
            ->orderBy('updated_at', 'desc')
            ->get();

        // Process bookings into conversations for the sidebar
        $conversations = [];
        foreach ($bookings as $booking) {
            $lastMessage = $booking->messages->first();
            $unreadCount = Message::where('booking_id', $booking->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();

            $conversations[] = (object)[
                'id' => $booking->id,
                'booking_id' => $booking->id,
                'student' => $booking->user,
                'hostel' => $booking->hostel,
                'last_message' => $lastMessage->message ?? 'No messages yet',
                'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                'unread_count' => $unreadCount,
                'is_selected' => false
            ];
        }

        // Get selected booking if provided
        $selectedBookingId = request('booking_id');
        $selectedConversation = null;

        if ($selectedBookingId) {
            $selectedBooking = Booking::with(['user', 'hostel', 'messages.sender'])
                ->find($selectedBookingId);

            if ($selectedBooking && $selectedBooking->hostel->landlord_id === Auth::id()) {
                $selectedConversation = (object)[
                    'id' => $selectedBooking->id,
                    'student' => $selectedBooking->user,
                    'hostel' => $selectedBooking->hostel,
                    'messages' => $selectedBooking->messages->map(function($message) {
                        return (object)[
                            'id' => $message->id,
                            'content' => $message->message,
                            'is_sender' => $message->sender_id === Auth::id(),
                            'created_at' => $message->created_at
                        ];
                    })
                ];

                // Mark messages as read
                Message::where('booking_id', $selectedBookingId)
                    ->where('receiver_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        $stats = [
            'total_messages' => Message::where('receiver_id', Auth::id())->count(),
            'unread_messages' => Message::where('receiver_id', Auth::id())->where('is_read', false)->count(),
            'active_conversations' => count($conversations),
        ];

        return view('landlord.messages.index', compact('conversations', 'selectedConversation', 'stats'));
    }

}
