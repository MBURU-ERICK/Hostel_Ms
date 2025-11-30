<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a new notification
     */
    public static function create($userId, $type, $title, $message, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Notify student about booking creation
     */
    public static function notifyBookingCreated($booking)
    {
        return self::create(
            $booking->user_id,
            'booking_created',
            'Booking Request Submitted',
            "Your booking request for {$booking->hostel->name} has been submitted and is pending confirmation.",
            ['booking_id' => $booking->id]
        );
    }

    /**
     * Notify landlord about new booking request
     */
    public static function notifyLandlordNewBooking($booking)
    {
        return self::create(
            $booking->hostel->landlord_id,
            'booking_created',
            'New Booking Request',
            "You have a new booking request for {$booking->hostel->name} from {$booking->user->name}.",
            ['booking_id' => $booking->id, 'hostel_id' => $booking->hostel_id]
        );
    }

    /**
     * Notify student about booking confirmation
     */
    public static function notifyBookingConfirmed($booking)
    {
        return self::create(
            $booking->user_id,
            'booking_confirmed',
            'Booking Confirmed!',
            "Your booking for {$booking->hostel->name} has been confirmed. You can now proceed with payment.",
            ['booking_id' => $booking->id]
        );
    }

    /**
     * Notify student about payment success
     */
    public static function notifyPaymentSuccess($payment)
    {
        return self::create(
            $payment->user_id,
            'payment_received',
            'Payment Successful',
            "Your payment of KSh " . number_format($payment->amount) . " for {$payment->booking->hostel->name} was successful.",
            ['payment_id' => $payment->id, 'booking_id' => $payment->booking_id]
        );
    }

    /**
     * Notify landlord about payment received
     */
    public static function notifyLandlordPaymentReceived($payment)
    {
        return self::create(
            $payment->booking->hostel->landlord_id,
            'payment_received',
            'Payment Received',
            "You have received KSh " . number_format($payment->amount) . " from {$payment->user->name} for {$payment->booking->hostel->name}.",
            ['payment_id' => $payment->id, 'booking_id' => $payment->booking_id]
        );
    }

    /**
     * Notify about new message
     */
    public static function notifyNewMessage($message)
    {
        $recipientId = $message->sender_id === $message->conversation->user_id
            ? $message->conversation->landlord_id
            : $message->conversation->user_id;

        return self::create(
            $recipientId,
            'message_received',
            'New Message',
            "You have a new message from " . ($message->sender_id === $message->conversation->user_id ? $message->conversation->user->name : $message->conversation->hostel->landlord->name),
            ['message_id' => $message->id, 'conversation_id' => $message->conversation_id]
        );
    }
    /**
 * Notify student about booking cancellation
 */
public static function notifyBookingCancelled($booking)
{
    return self::create(
        $booking->user_id,
        'booking_cancelled',
        'Booking Cancelled',
        "Your booking for {$booking->hostel->name} has been cancelled by the landlord.",
        ['booking_id' => $booking->id]
    );
}

    /**
     * Get unread notifications count for user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecentNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public static function getAllNotifications($userId, $perPage = 15)
    {
        return Notification::where('user_id', $userId)
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }
}
