<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ActivityBookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\TestDashboardController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\GuestDashboardController;
use App\Http\Controllers\KitchenController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [\App\Http\Controllers\PublicWebsiteController::class, 'index'])->name('home');
Route::get('/gallery', [\App\Http\Controllers\PublicWebsiteController::class, 'gallery'])->name('gallery');
Route::get('/amenities', [\App\Http\Controllers\PublicWebsiteController::class, 'amenities'])->name('amenities');
Route::get('/contact', [\App\Http\Controllers\PublicWebsiteController::class, 'contact'])->name('contact');
Route::get('/api/check-availability', [\App\Http\Controllers\PublicWebsiteController::class, 'checkAvailability'])->name('api.check-availability');

// Public room booking routes (no authentication required)
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/rooms', [BookingController::class, 'publicIndex'])->name('rooms.index');
    Route::get('/rooms/create', [BookingController::class, 'publicCreate'])->name('rooms.create');
    Route::post('/rooms', [BookingController::class, 'publicStore'])->name('rooms.store');
    Route::post('/coupon/validate', [\App\Http\Controllers\CouponController::class, 'validateCoupon'])->name('coupon.validate');
});

// Public Payment Routes
Route::get('/payment/checkout/{payment}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/stripe/webhook', [\App\Http\Controllers\WebhookController::class, 'handleWebhook'])->name('cashier.webhook');

// Public food ordering routes (no authentication required)
Route::prefix('food')->name('food.')->group(function () {
    Route::get('/menu', [FoodOrderController::class, 'menu'])->name('menu');
    Route::get('/order/{food}', [FoodOrderController::class, 'orderForm'])->name('order');
    Route::post('/order', [FoodOrderController::class, 'store'])->name('store');
    Route::get('/confirmation/{foodOrder}', [FoodOrderController::class, 'confirmation'])->name('confirmation');
    Route::get('/my-orders', [FoodOrderController::class, 'myOrders'])->name('my-orders');
});

// Public activity booking routes (no authentication required)
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/activities', [ActivityBookingController::class, 'publicIndex'])->name('activities.index');
    Route::get('/activities/create', [ActivityBookingController::class, 'publicCreate'])->name('activities.create');
    Route::post('/activities', [ActivityBookingController::class, 'publicStore'])->name('activities.store');
    Route::get('/activities/available-slots', [ActivityBookingController::class, 'getAvailableSlots'])->name('activities.available-slots');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'verified', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-test', [TestDashboardController::class, 'index'])->name('dashboard.test');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/available-rooms', [BookingController::class, 'getAvailableRooms'])->name('bookings.available-rooms');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('/bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out');

    // Activities
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/calendar', [ActivityBookingController::class, 'calendar'])->name('calendar');
        Route::get('/available-slots', [ActivityBookingController::class, 'getAvailableSlots'])->name('available-slots');

        // Activity Bookings
        Route::prefix('/bookings')->name('bookings.')->group(function () {
            Route::get('/', [ActivityBookingController::class, 'index'])->name('index');
            Route::get('/create', [ActivityBookingController::class, 'create'])->name('create');
            Route::post('/', [ActivityBookingController::class, 'store'])->name('store');
            Route::get('/{activityBooking}', [ActivityBookingController::class, 'show'])->name('show');
            Route::post('/{activityBooking}/confirm', [ActivityBookingController::class, 'confirm'])->name('confirm');
            Route::post('/{activityBooking}/complete', [ActivityBookingController::class, 'complete'])->name('complete');
            Route::post('/{activityBooking}/cancel', [ActivityBookingController::class, 'cancel'])->name('cancel');
        });
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/reports', [PaymentController::class, 'reports'])->name('reports');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::get('/api/pending', [PaymentController::class, 'getPendingPayments'])->name('api.pending');
        Route::get('/api/stats', [PaymentController::class, 'dashboardStats'])->name('api.stats');
        Route::post('/', [PaymentController::class, 'store'])->name('store');

        // Wildcard routes must come last
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
        Route::post('/{payment}/complete', [PaymentController::class, 'complete'])->name('complete');
        Route::post('/{payment}/capture', [PaymentController::class, 'capture'])->name('capture');
        Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::get('/{payment}/generate-invoice', [PaymentController::class, 'generateInvoice'])->name('generate-invoice');
        Route::get('/invoice/{invoice}/download', [PaymentController::class, 'downloadInvoice'])->name('download-invoice');
    });


    // Rooms
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/housekeeping', [RoomController::class, 'housekeeping'])->name('housekeeping');
        Route::get('/maintenance', [RoomController::class, 'maintenance'])->name('maintenance');
        Route::get('/availability', [RoomController::class, 'availability'])->name('availability');
        Route::get('/floor-plan', [RoomController::class, 'floorPlan'])->name('floor-plan');
        Route::get('/{room}', [RoomController::class, 'show'])->name('show');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
        Route::post('/{room}/update-status', [RoomController::class, 'updateStatus'])->name('update-status');
        Route::get('/api/available', [RoomController::class, 'getAvailableRooms'])->name('api.available');
        Route::get('/api/statistics', [RoomController::class, 'statistics'])->name('api.statistics');
    });

    // Guests
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [GuestController::class, 'index'])->name('index');
        Route::get('/create', [GuestController::class, 'create'])->name('create');
        Route::post('/', [GuestController::class, 'store'])->name('store');
        Route::get('/search', [GuestController::class, 'search'])->name('search');
        Route::get('/loyalty', [GuestController::class, 'loyalty'])->name('loyalty');
        Route::get('/vip', [GuestController::class, 'vipGuests'])->name('vip');
        Route::get('/check-in-today', [GuestController::class, 'checkInToday'])->name('check-in-today');
        Route::get('/check-out-today', [GuestController::class, 'checkOutToday'])->name('check-out-today');
        Route::get('/export', [GuestController::class, 'export'])->name('export');
        Route::get('/api/statistics', [GuestController::class, 'statistics'])->name('api.statistics');

        // Wildcards last
        Route::get('/{guest}', [GuestController::class, 'show'])->name('show');
        Route::get('/{guest}/edit', [GuestController::class, 'edit'])->name('edit');
        Route::put('/{guest}', [GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [GuestController::class, 'destroy'])->name('destroy');
    });

    // Food Management
    Route::prefix('food')->name('food.')->group(function () {
        // Food Items
        Route::get('/items', [FoodOrderController::class, 'adminIndex'])->name('items.index');
        Route::get('/items/create', [FoodOrderController::class, 'adminCreate'])->name('items.create');
        Route::post('/items', [FoodOrderController::class, 'adminStore'])->name('items.store');
        Route::get('/items/{food}', [FoodOrderController::class, 'adminShow'])->name('items.show');
        Route::get('/items/{food}/edit', [FoodOrderController::class, 'adminEdit'])->name('items.edit');
        Route::put('/items/{food}', [FoodOrderController::class, 'adminUpdate'])->name('items.update');
        Route::delete('/items/{food}', [FoodOrderController::class, 'adminDestroy'])->name('items.destroy');

        // Food Orders
        Route::get('/orders', [FoodOrderController::class, 'adminOrders'])->name('orders.index');
        Route::get('/orders/{foodOrder}', [FoodOrderController::class, 'adminOrderShow'])->name('orders.show');
        Route::post('/orders/{foodOrder}/status', [FoodOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{foodOrder}/complete', [FoodOrderController::class, 'completeOrder'])->name('orders.complete');
        Route::post('/orders/{foodOrder}/cancel', [FoodOrderController::class, 'cancelOrder'])->name('orders.cancel');
    });

    // Kitchen Management
    Route::prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/dashboard', [KitchenController::class, 'dashboard'])->name('dashboard');
        Route::get('/kot/{foodOrder}', [KitchenController::class, 'kotTicket'])->name('kot-ticket');
        Route::get('/print/kot/{foodOrder}', [KitchenController::class, 'printKot'])->name('print-kot');
        Route::post('/orders/{foodOrder}/status', [KitchenController::class, 'updateOrderStatus'])->name('update-status');
        Route::get('/active-orders', [KitchenController::class, 'activeOrders'])->name('active-orders');
        Route::get('/order-history', [KitchenController::class, 'orderHistory'])->name('order-history');
    });

    // Inventory & Suppliers
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('store');
        Route::get('/{item}/edit', [\App\Http\Controllers\Admin\InventoryController::class, 'edit'])->name('edit');
        Route::put('/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'update'])->name('update');
        Route::get('/{item}/history', [\App\Http\Controllers\Admin\InventoryController::class, 'history'])->name('history');
        Route::get('/api/alerts', [\App\Http\Controllers\Admin\InventoryController::class, 'lowStockAlerts'])->name('api.alerts');
    });

    // Coupon Management
    Route::resource('coupons', \App\Http\Controllers\CouponController::class);

    // Gallery Management
    Route::resource('gallery', \App\Http\Controllers\Admin\GalleryController::class);

    // Amenities Management
    Route::resource('amenities', \App\Http\Controllers\Admin\AmenityController::class);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // Security Features
    Route::prefix('security')->name('security.')->group(function () {
        // Audit Logs
        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('index');
            Route::get('/{auditLog}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('show');
            Route::get('/export/csv', [\App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('export');
        });

        // Backup & Restore
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
            Route::post('/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
            Route::get('/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
        });

        // Activity Safety Logs
        Route::prefix('activity-safety')->name('activity-safety.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ActivitySafetyLogController::class, 'index'])->name('index');
            Route::get('/create/{activityBooking}', [\App\Http\Controllers\Admin\ActivitySafetyLogController::class, 'create'])->name('create');
            Route::post('/store/{activityBooking}', [\App\Http\Controllers\Admin\ActivitySafetyLogController::class, 'store'])->name('store');
            Route::get('/{activitySafety}', [\App\Http\Controllers\Admin\ActivitySafetyLogController::class, 'show'])->name('show');
            Route::get('/{activitySafety}/edit', [\App\Http\Controllers\Admin\ActivitySafetyLogController::class, 'edit'])->name('edit');
            Route::put('/{activitySafety}', [\App\Http\Controllers\Admin\ActivitySafetyLogController::class, 'update'])->name('update');
        });
    });

    // Feedback Management
    Route::resource('feedback', \App\Http\Controllers\Admin\FeedbackController::class)->only(['index', 'show', 'update', 'destroy']);
});

// Default dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Guest Dashboard
    Route::prefix('guest')->name('guest.')->group(function () {
        Route::get('/dashboard', [GuestDashboardController::class, 'index'])->name('dashboard');
        Route::get('/food-menu', [GuestDashboardController::class, 'foodMenu'])->name('food-menu');
        Route::get('/order-food/{food}', [GuestDashboardController::class, 'orderFood'])->name('order-food');
        Route::post('/store-order', [GuestDashboardController::class, 'storeOrder'])->name('store-order');
        Route::get('/my-orders', [GuestDashboardController::class, 'myOrders'])->name('my-orders');
        Route::get('/notifications', [GuestDashboardController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [GuestDashboardController::class, 'markNotificationRead'])->name('mark-notification-read');

        // Guest Feedback
        Route::get('/feedback/create', [GuestDashboardController::class, 'createFeedback'])->name('feedback.create');
        Route::post('/feedback', [GuestDashboardController::class, 'storeFeedback'])->name('feedback.store');
    });
});

require __DIR__ . '/auth.php';
