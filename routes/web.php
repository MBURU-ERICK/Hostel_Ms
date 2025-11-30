<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\StudentMiddleware;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\StudentServiceController;


Route::get('/', function () {
    return view('welcome');
});

// Fortify Auth Routes (make sure these are included)
require __DIR__.'/fortify.php';

// Student Routes with inline middleware
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/search-hostels', [StudentController::class, 'searchHostels'])->name('search-hostels');
    Route::get('/hostel/{id}', [StudentController::class, 'viewHostel'])->name('view-hostel');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::post('/profile', [StudentController::class, 'updateProfile'])->name('update-profile');
    Route::post('/hostel/{hostel}/book', [StudentController::class, 'bookHostel'])->name('book-hostel');
    Route::get('/booking-confirmation/{booking}', [StudentController::class, 'bookingConfirmation'])->name('booking-confirmation');
    Route::get('/my-bookings', [StudentController::class, 'myBookings'])->name('my-bookings');
    Route::post('/booking/{booking}/cancel', [StudentController::class, 'cancelBooking'])->name('cancel-booking');


    // Student notifications route
    Route::get('/notifications', [StudentController::class, 'notifications'])->name('notifications');

// Messaging routes
Route::get('/messages/{bookingId}', [MessageController::class, 'index'])->name('messages.index');
Route::post('/messages/{bookingId}', [MessageController::class, 'store'])->name('messages.store');
Route::get('/messages/{bookingId}/get', [MessageController::class, 'getMessages'])->name('messages.get');
Route::get('/messages/unread/count', [MessageController::class, 'getUnreadCount'])->name('messages.unread.count');
});

// Landlord Routes
Route::middleware(['auth', 'verified'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('dashboard');

    // Hostels
    Route::get('/hostels', [LandlordController::class, 'hostels'])->name('hostels');
    Route::get('/hostels/create', [LandlordController::class, 'createHostel'])->name('hostels.create');
    Route::post('/hostels', [LandlordController::class, 'storeHostel'])->name('hostels.store');
    Route::get('/hostels/{id}/edit', [LandlordController::class, 'editHostel'])->name('hostels.edit');
Route::patch('/hostels/{id}/toggle-availability', [LandlordController::class, 'toggleAvailability'])->name('hostels.toggle-availability');
    Route::put('/hostels/{id}', [LandlordController::class, 'updateHostel'])->name('hostels.update');
    Route::get('/hostels/{id}/delete', [LandlordController::class, 'deleteHostel'])->name('hostels.delete');
Route::delete('/hostels/{id}', [LandlordController::class, 'destroyHostel'])->name('hostels.destroy');

    // Bookings
    Route::get('/bookings', [LandlordController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{id}/status', [LandlordController::class, 'updateBookingStatus'])->name('bookings.update-status');

    // Messages
    Route::get('/messages', [LandlordController::class, 'messages'])->name('messages');

    // Reviews
    Route::get('/reviews', [LandlordController::class, 'reviews'])->name('reviews');
    // routes/web.php
Route::get('/landlord/reviews', [LandlordController::class, 'reviews'])->name('landlord.reviews');
    // Earnings - ADD THIS MISSING ROUTE
    Route::get('/earnings', [LandlordController::class, 'earnings'])->name('earnings');
    // routes/web.php

    Route::get('/messages', [MessageController::class, 'landlordMessages'])->name('messages');
    Route::post('/message/send', [MessageController::class, 'store'])->name('messages.send');

    Route::get('/landlord/messages/conversation/{conversation}', [MessageController::class, 'showConversation'])->name('landlord.messages.conversation');
    Route::post('/landlord/reviews/{review}/respond', [LandlordController::class, 'respondToReview'])->name('landlord.reviews.respond');
});

// Notification routes
Route::prefix('notifications')->middleware(['auth', 'verified'])->group(function () {
    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/{notification}', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
});



// Unread messages count
Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount'])->name('messages.unread-count');

// Favorites routes
Route::middleware(['auth', 'verified'])->prefix('favorites')->group(function () {
    Route::get('/', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/{hostel}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/{hostel}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

// Reviews routes
Route::middleware(['auth', 'verified'])->prefix('reviews')->group(function () {
    Route::get('/create/{booking}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/store/{booking}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/hostel/{hostel}', [ReviewController::class, 'hostelReviews'])->name('reviews.hostel');
});
// Student Booking Routes
Route::get('/student/my-bookings', [StudentController::class, 'myBookings'])->name('student.my-bookings');
Route::get('/student/booking/{booking}', [StudentController::class, 'bookingDetails'])->name('student.booking.details');
Route::post('/student/bookings/{booking}/cancel', [StudentController::class, 'cancelBooking'])->name('student.bookings.cancel');
Route::get('/student/booking/{booking}/edit', [StudentController::class, 'editBooking'])->name('student.booking.edit');
Route::put('/student/booking/{booking}', [StudentController::class, 'updateBooking'])->name('student.booking.update');


// Student Payment Routes
Route::get('/student/payment/{booking}/make', [PaymentController::class, 'showPaymentForm'])->name('student.payment.make');
Route::post('/student/payment/{booking}/initiate', [PaymentController::class, 'initiatePayment'])->name('student.payment.initiate');
Route::post('/student/payment/status', [PaymentController::class, 'checkPaymentStatus'])->name('student.payment.status');
Route::post('/student/payment/{payment}/retry', [PaymentController::class, 'retryPayment'])->name('student.payment.retry');
Route::get('/student/payment-history', [PaymentController::class, 'paymentHistory'])->name('student.payment.history');

// M-Pesa Callback Route (should be publicly accessible)
Route::post('/api/mpesa/callback', [PaymentController::class, 'mpesaCallback'])->name('mpesa.callback');

// Redirect based on user type
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    } elseif ($user->isLandlord()) {
        return redirect()->route('landlord.dashboard');
    } elseif ($user->isServiceProvider()) {
        return redirect()->route('service-provider.dashboard');
    } elseif ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    // Default redirect if no role is detected
    return redirect('/');
})->middleware(['auth', 'verified']);
// Service Provider Routes
Route::prefix('service-provider')->name('service-provider.')->group(function () {
    Route::get('/dashboard', [ServiceProviderController::class, 'dashboard'])->name('dashboard');
    Route::get('/setup', [ServiceProviderController::class, 'setup'])->name('setup');
    Route::post('/setup', [ServiceProviderController::class, 'storeSetup'])->name('store-setup');
    Route::get('/requests', [ServiceProviderController::class, 'serviceRequests'])->name('requests');
    Route::post('/requests/{id}/accept', [ServiceProviderController::class, 'acceptRequest'])->name('requests.accept');
    Route::post('/requests/{id}/start', [ServiceProviderController::class, 'startJob'])->name('requests.start');
    Route::post('/requests/{id}/complete', [ServiceProviderController::class, 'completeJob'])->name('requests.complete');
    Route::get('/earnings', [ServiceProviderController::class, 'earnings'])->name('earnings');
    Route::get('/reviews', [ServiceProviderController::class, 'reviews'])->name('reviews');
    Route::get('/profile', [ServiceProviderController::class, 'profile'])->name('profile');
    Route::post('/profile', [ServiceProviderController::class, 'updateProfile'])->name('profile.update');
        Route::get('/messages', [ServiceProviderController::class, 'messages'])->name('messages');
    Route::post('/messages/send', [ServiceProviderController::class, 'sendMessage'])->name('messages.send');
});

// Student Service Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/services', [StudentServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [StudentServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [StudentServiceController::class, 'store'])->name('services.store');
    Route::post('/services/{id}/rate', [StudentServiceController::class, 'rateService'])->name('services.rate');
});
