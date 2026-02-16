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
use App\Http\Controllers\Admin\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Middleware\LandlordMiddleware;
use App\Http\Middleware\ServiceProviderMiddleware;
use App\Http\Middleware\CheckApprovalStatus;
use App\Http\Controllers\Admin\AdminMessageController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/approval-pending', function () {
    return view('auth.approval-pending');
})->name('approval.pending')->middleware('auth');

// Fortify Auth Routes
require __DIR__.'/fortify.php';

// Shared middleware group for authenticated routes
$authMiddleware = ['auth', 'verified'];

// Student Routes - Consolidated
Route::middleware($authMiddleware)->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard')->middleware(StudentMiddleware::class);
    Route::get('/search-hostels', [StudentController::class, 'searchHostels'])->name('search-hostels')->middleware(StudentMiddleware::class);
    Route::get('/hostel/{id}', [StudentController::class, 'viewHostel'])->name('view-hostel')->middleware(StudentMiddleware::class);
    Route::get('/messages', [StudentController::class, 'messages'])->name('messages')->middleware(StudentMiddleware::class);

    // Profile routes
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile')->middleware(StudentMiddleware::class);
    Route::post('/profile', [StudentController::class, 'updateProfile'])->name('update-profile')->middleware(StudentMiddleware::class);

    // Booking routes
    Route::post('/hostel/{hostel}/book', [StudentController::class, 'bookHostel'])->name('book-hostel')->middleware(StudentMiddleware::class);
    Route::get('/booking-confirmation/{booking}', [StudentController::class, 'bookingConfirmation'])->name('booking-confirmation')->middleware(StudentMiddleware::class);
    Route::get('/my-bookings', [StudentController::class, 'myBookings'])->name('my-bookings')->middleware(StudentMiddleware::class);
    Route::post('/booking/{booking}/cancel', [StudentController::class, 'cancelBooking'])->name('cancel-booking')->middleware(StudentMiddleware::class);
    Route::get('/booking/{booking}', [StudentController::class, 'bookingDetails'])->name('booking.details')->middleware(StudentMiddleware::class);
    Route::get('/booking/{booking}/edit', [StudentController::class, 'editBooking'])->name('booking.edit')->middleware(StudentMiddleware::class);
    Route::put('/booking/{booking}', [StudentController::class, 'updateBooking'])->name('booking.update')->middleware(StudentMiddleware::class);

    // Notification routes
    Route::get('/notifications', [StudentController::class, 'notifications'])->name('notifications')->middleware(StudentMiddleware::class);

    // Payment routes
    Route::get('/payment/{booking}', [PaymentController::class, 'showPaymentForm'])->name('payment.form')->middleware(StudentMiddleware::class);
    Route::post('/payment/{booking}/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate')->middleware(StudentMiddleware::class);
    Route::post('/payment/status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.status')->middleware(StudentMiddleware::class);
    Route::post('/payment/{payment}/retry', [PaymentController::class, 'retryPayment'])->name('payment.retry')->middleware(StudentMiddleware::class);
    Route::get('/payment-history', [PaymentController::class, 'paymentHistory'])->name('payment.history')->middleware(StudentMiddleware::class);
    Route::post('/student/payment/update-status', [PaymentController::class, 'updatePaymentStatus'])->name('student.payment.update-status');
    Route::get('/student/payment/recent-updates', [PaymentController::class, 'getRecentUpdates'])->name('student.payment.recent-updates');
    Route::get('/student/booking/{bookingId}/invoice', [PaymentController::class, 'getInvoice'])->name('student.booking.invoice');
        Route::get('/payment/{bookingId}', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/{bookingId}/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    
    // Add this missing route
    Route::post('/payment/update-status', [PaymentController::class, 'updatePaymentStatus'])->name('payment.update-status');
    
    Route::get('/payment/recent-updates', [PaymentController::class, 'getRecentUpdates'])->name('payment.recent-updates');
    Route::get('/booking/{bookingId}/invoice', [PaymentController::class, 'getInvoice'])->name('booking.invoice');
    Route::get('/payment-history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    Route::post('/payment/{paymentId}/retry', [PaymentController::class, 'retryPayment'])->name('payment.retry');
    Route::post('/payment/{paymentId}/cancel', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');
    Route::get('/payment/{paymentId}/details', [PaymentController::class, 'getPaymentDetails'])->name('payment.details');
    Route::get('/payment-stats', [PaymentController::class, 'getPaymentStats'])->name('payment.stats');
    Route::post('/payment/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.check-status');
        // Service Request routes - ALL handled by ServiceRequestController
      // Service Request routes - FIXED
    Route::get('/services', [ServiceRequestController::class, 'studentIndex'])->name('services.index');
    Route::get('/services/create', [ServiceRequestController::class, 'studentCreate'])->name('services.create');
    Route::post('/services', [ServiceRequestController::class, 'store'])->name('services.store');
    Route::post('/services/{id}/rate', [ServiceRequestController::class, 'rateService'])->name('services.rate');
    
    // Service request chat
    Route::get('/service-requests/{serviceRequest}/chat', [ServiceRequestController::class, 'chat'])->name('service-requests.chat');
    
        // Messages
    Route::get('/messages', [MessageController::class, 'studentMessages'])->name('messages');
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'getStudentConversationMessages'])->name('conversations.messages');
    Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::post('/messages/start-conversation', [MessageController::class, 'startStudentConversation'])->name('messages.start-conversation');

    // Data endpoints
    Route::get('/get-landlords', [MessageController::class, 'getStudentLandlords'])->name('get-landlords');
    Route::get('/get-service-providers', [MessageController::class, 'getStudentServiceProviders'])->name('get-service-providers');

    // Service Requests
    //Route::post('/service-requests', [MessageController::class, 'createStudentServiceRequest'])->name('service-requests.create');
});

// Service Request Routes
Route::prefix('service-requests')->name('service-requests.')->group(function () {
    Route::post('/{serviceRequest}/send-message', [ServiceRequestController::class, 'sendMessage'])->name('send-message');
    Route::get('/{serviceRequest}/conversation', [ServiceRequestController::class, 'getConversation'])->name('conversation');
    Route::post('/{serviceRequest}/update-status', [ServiceRequestController::class, 'updateStatus'])->name('update-status');
});

// Landlord Routes - Consolidated
Route::middleware($authMiddleware)->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('dashboard')->middleware(LandlordMiddleware::class);
    Route::get('/earnings', [LandlordController::class, 'earnings'])->name('earnings')->middleware(LandlordMiddleware::class);
    Route::get('/bookings', [LandlordController::class, 'bookings'])->name('bookings')->middleware(LandlordMiddleware::class);
    Route::post('/bookings/{id}/status', [LandlordController::class, 'updateBookingStatus'])->name('bookings.update-status')->middleware(LandlordMiddleware::class);

    // Hostel routes
    Route::get('/hostels', [LandlordController::class, 'hostels'])->name('hostels')->middleware(LandlordMiddleware::class);
    Route::get('/hostels/create', [LandlordController::class, 'createHostel'])->name('hostels.create')->middleware(LandlordMiddleware::class);
    Route::post('/hostels', [LandlordController::class, 'storeHostel'])->name('hostels.store')->middleware(LandlordMiddleware::class);
    Route::get('/hostels/{id}/edit', [LandlordController::class, 'editHostel'])->name('hostels.edit')->middleware(LandlordMiddleware::class);
    Route::put('/hostels/{id}', [LandlordController::class, 'updateHostel'])->name('hostels.update')->middleware(LandlordMiddleware::class);
    
       Route::get('/payments', [LandlordController::class, 'payments'])->name('payments.index');
    Route::get('/payments/{id}', [LandlordController::class, 'showPayment'])->name('payments.show');
    Route::get('/payments/export/csv', [LandlordController::class, 'exportPayments'])->name('payments.export');
    Route::patch('/hostels/{id}/toggle-availability', [LandlordController::class, 'toggleAvailability'])->name('hostels.toggle-availability')->middleware(LandlordMiddleware::class);
    
    Route::get('/hostels/{id}/delete', [LandlordController::class, 'deleteHostel'])->name('hostels.delete')->middleware(LandlordMiddleware::class);
    Route::delete('/hostels/{id}', [LandlordController::class, 'destroyHostel'])->name('hostels.destroy')->middleware(LandlordMiddleware::class);
    Route::prefix('messages')->name('messages.')->middleware(LandlordMiddleware::class)->group(function () {
        // Main messages page
        Route::get('/', [MessageController::class, 'landlordMessages'])->name('index');

        // Standalone send message page
        Route::get('/send', [MessageController::class, 'create'])->name('send');
        Route::post('/send', [MessageController::class, 'store'])->name('send.store');

        // Conversation management
        Route::post('/start-conversation', [MessageController::class, 'startConversation'])->name('start-conversation');
        Route::post('/{conversation}/send-message', [MessageController::class, 'sendMessageInConversation'])->name('send-message');
        Route::post('/{conversation}/mark-read', [MessageController::class, 'markAsRead'])->name('mark-read');
        Route::get('/{conversation}', [MessageController::class, 'showConversation'])->name('conversation');

        // Recipient data - FIXED ROUTE NAMES
        Route::get('/get-students', [MessageController::class, 'getStudents'])->name('get-students');
        //Route::get('/get-service-providers', [MessageController::class, 'getServiceProviders'])->name('get-service-providers');
            
        Route::get('/get-service-providers', [MessageController::class, 'getServiceProviders'])->name('get-service-providers');
        // Additional message actions
        Route::delete('/{conversation}', [MessageController::class, 'deleteConversation'])->name('delete');
        Route::post('/{conversation}/archive', [MessageController::class, 'archiveConversation'])->name('archive');

        // Statistics
        Route::get('/stats/message-stats', [MessageController::class, 'getLandlordMessageStats'])->name('stats.message-stats');
    });


    // Review routes
    Route::get('/reviews', [LandlordController::class, 'reviews'])->name('reviews')->middleware(LandlordMiddleware::class);
    Route::post('/reviews/{review}/respond', [LandlordController::class, 'respondToReview'])->name('reviews.respond')->middleware(LandlordMiddleware::class);
});

// Service Provider Routes - Consolidated
Route::middleware($authMiddleware)->prefix('service-provider')->name('service-provider.')->group(function () {
    Route::get('/dashboard', [ServiceProviderController::class, 'dashboard'])->name('dashboard')->middleware(ServiceProviderMiddleware::class);
    Route::get('/setup', [ServiceProviderController::class, 'setup'])->name('setup')->middleware(ServiceProviderMiddleware::class);
    Route::post('/setup', [ServiceProviderController::class, 'storeSetup'])->name('store-setup')->middleware(ServiceProviderMiddleware::class);
    Route::get('/requests', [ServiceProviderController::class, 'serviceRequests'])->name('requests')->middleware(ServiceProviderMiddleware::class);
    Route::post('/requests/{id}/accept', [ServiceProviderController::class, 'acceptRequest'])->name('requests.accept')->middleware(ServiceProviderMiddleware::class);
    Route::post('/requests/{id}/start', [ServiceProviderController::class, 'startJob'])->name('requests.start')->middleware(ServiceProviderMiddleware::class);
    Route::post('/requests/{id}/complete', [ServiceProviderController::class, 'completeJob'])->name('requests.complete')->middleware(ServiceProviderMiddleware::class);
    Route::get('/earnings', [ServiceProviderController::class, 'earnings'])->name('earnings')->middleware(ServiceProviderMiddleware::class);
    Route::get('/reviews', [ServiceProviderController::class, 'reviews'])->name('reviews')->middleware(ServiceProviderMiddleware::class);
    Route::get('/profile', [ServiceProviderController::class, 'profile'])->name('profile')->middleware(ServiceProviderMiddleware::class);
    Route::post('/update-profile', [ServiceProviderController::class, 'updateProfile'])->name('update-profile')->middleware(ServiceProviderMiddleware::class);
    Route::get('/messages', [MessageController::class, 'serviceProviderMessages'])->name('messages')->middleware(ServiceProviderMiddleware::class);
    Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send')->middleware(ServiceProviderMiddleware::class);
    Route::post('/messages/start-conversation', [MessageController::class, 'startConversation'])->name('messages.start-conversation')->middleware(ServiceProviderMiddleware::class);
    Route::get('/get-students', [MessageController::class, 'getServiceProviderStudents'])->name('get-students')->middleware(ServiceProviderMiddleware::class);
    Route::get('/get-landlords', [MessageController::class, 'getServiceProviderLandlords'])->name('get-landlords')->middleware(ServiceProviderMiddleware::class);
    Route::get('/service-requests/{serviceRequest}/chat', [ServiceRequestController::class, 'chat'])->name('service-requests.chat')->middleware(ServiceProviderMiddleware::class);
});

// Admin Routes - Consolidated
Route::middleware($authMiddleware)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard')->middleware(AdminMiddleware::class);
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics')->middleware(AdminMiddleware::class);
    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('settings')->middleware(AdminMiddleware::class);
    Route::post('/settings', [AdminController::class, 'updateSystemSettings'])->name('settings.update')->middleware(AdminMiddleware::class);

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users')->middleware(AdminMiddleware::class);
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show')->middleware(AdminMiddleware::class);
    Route::match(['PUT', 'POST'], 'users/{id}', [AdminController::class, 'updateUser'])->name('users.update')->middleware(AdminMiddleware::class);
    Route::post('/users/{id}/approve', [AdminController::class, 'approve'])->name('users.approve')->middleware(AdminMiddleware::class);
    Route::post('/users/{id}/suspend', [AdminController::class, 'suspend'])->name('users.suspend')->middleware(AdminMiddleware::class);
    Route::post('/users/{id}/activate', [AdminController::class, 'activate'])->name('users.activate')->middleware(AdminMiddleware::class);
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy')->middleware(AdminMiddleware::class);

    // Hostel management
    Route::get('/hostels', [AdminController::class, 'hostels'])->name('hostels')->middleware(AdminMiddleware::class);
    Route::get('/hostels/{id}', [AdminController::class, 'showHostel'])->name('hostels.show')->middleware(AdminMiddleware::class);
    Route::match(['PUT', 'POST'], '/hostels/{id}', [AdminController::class, 'updateHostel'])->name('hostels.update')->middleware(AdminMiddleware::class);
    Route::put('/hostels/{id}/availability', [AdminController::class, 'updateHostelAvailability'])->name('hostels.availability')->middleware(AdminMiddleware::class);
    Route::put('/hostels/{id}/reject', [AdminController::class, 'rejectHostel'])->name('hostels.reject')->middleware(AdminMiddleware::class);
    Route::post('/hostels/{id}/approve', [AdminController::class, 'approveHostel'])->name('hostels.approve')->middleware(AdminMiddleware::class);
    Route::delete('/hostels/{id}', [AdminController::class, 'destroyHostel'])->name('hostels.destroy')->middleware(AdminMiddleware::class);
    Route::post('/hostels/{id}/images', [AdminController::class, 'uploadHostelImages'])->name('hostels.images.upload')->middleware(AdminMiddleware::class);
Route::delete('/hostels/{id}/images', [AdminController::class, 'deleteHostelImage'])->name('hostels.images.delete')->middleware(AdminMiddleware::class);
        Route::get('/bookings/{id}/details', [AdminController::class, 'getBookingDetails'])->name('bookings.details');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
    Route::get('/payments/{id}', [AdminController::class, 'showPayment'])->name('payments.show');
    // Booking management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings')->middleware(AdminMiddleware::class);
    Route::match(['PUT', 'POST'], '/bookings/{id}', [AdminController::class, 'updateBooking'])->name('bookings.update')->middleware(AdminMiddleware::class);
    // Service management
    Route::get('/service-requests', [AdminController::class, 'serviceRequests'])->name('service-requests')->middleware(AdminMiddleware::class);
    Route::get('/service-providers', [AdminController::class, 'serviceProviders'])->name('service-providers')->middleware(AdminMiddleware::class);
    Route::post('/service-providers/{id}/verify', [AdminController::class, 'verifyServiceProvider'])->name('service-providers.verify')->middleware(AdminMiddleware::class);
        // Bulk approval routes
    Route::get('/users/bulk-approval', [AdminController::class, 'bulkApproval'])->name('users.bulk-approval');
    Route::post('/users/bulk-approve', [AdminController::class, 'bulkApproveUsers'])->name('users.bulk-approve');
    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages');
    Route::post('/messages/broadcast', [AdminMessageController::class, 'sendToAll'])->name('messages.broadcast');
    Route::post('/messages/send', [AdminMessageController::class, 'sendToUser'])->name('messages.send');
});

// Shared Feature Routes
Route::middleware($authMiddleware)->group(function () {
    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'delete'])->name('notifications.delete');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    });

    // Favorite routes
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('favorites.index')->middleware(StudentMiddleware::class);
        Route::post('/{hostel}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle')->middleware(StudentMiddleware::class);
        Route::delete('/{hostel}', [FavoriteController::class, 'destroy'])->name('favorites.destroy')->middleware(StudentMiddleware::class);
    });

    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::get('/create/{booking}', [ReviewController::class, 'create'])->name('reviews.create')->middleware(StudentMiddleware::class);
        Route::post('/store/{booking}', [ReviewController::class, 'store'])->name('reviews.store')->middleware(StudentMiddleware::class);
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit')->middleware(StudentMiddleware::class);
        Route::put('/{review}', [ReviewController::class, 'update'])->name('reviews.update')->middleware(StudentMiddleware::class);
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware(StudentMiddleware::class);
        Route::get('/hostel/{hostel}', [ReviewController::class, 'hostelReviews'])->name('reviews.hostel')->middleware(StudentMiddleware::class);
    });

    // Message routes
    Route::prefix('messages')->group(function () {
        Route::get('/{bookingId}', [MessageController::class, 'index'])->name('messages.index');
        Route::post('/{bookingId}', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/{bookingId}/get', [MessageController::class, 'getMessages'])->name('messages.get');
        Route::get('/unread/count', [MessageController::class, 'getUnreadCount'])->name('messages.unread.count');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('messages.unread-count');
        Route::get('/conversations', [MessageController::class, 'getConversations'])->name('messages.conversations');
        Route::post('/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
        Route::get('/stats', [MessageController::class, 'getMessageStats'])->name('messages.stats');
    });
});

// Public routes
Route::post('/api/mpesa/callback', [PaymentController::class, 'mpesaCallback'])->name('mpesa.callback')->withoutMiddleware($authMiddleware);

// Dashboard routes based on user type

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Check if user is not approved (landlord or service provider)
    if (!$user->is_approved && in_array($user->user_type, ['landlord', 'service_provider'])) {
        return view('auth.approval-pending');
    }

    // Redirect based on user type for approved users
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isLandlord()) {
        return redirect()->route('landlord.dashboard');
    } elseif ($user->isServiceProvider()) {
        return redirect()->route('service-provider.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->name('dashboard')->middleware(['auth']);

// Approval pending page
Route::get('/approval-pending', function () {
    $user = auth()->user();

    // Only show this page to unapproved landlords/service providers
    if ($user->is_approved || $user->user_type === 'student') {
        return redirect()->route('dashboard');
    }

    return view('auth.approval-pending');
})->name('approval.pending')->middleware(CheckApprovalStatus::class);
