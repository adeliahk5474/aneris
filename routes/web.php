<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommissionServiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminHomeSettingController;

/* ===============================
   HOME
=============================== */

Route::get('/', [HomeController::class, 'index'])->name('home');

/* ===============================
   EXPLORE
=============================== */
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

/* ===============================
   AUTH
=============================== */
Route::get('/auth',           [AuthController::class, 'showAuthForm'])->name('auth.form');
Route::get('/auth/login',     [AuthController::class, 'showAuthForm'])->name('login');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login',    [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout',   [AuthController::class, 'logout'])->name('auth.logout');

/* ===============================
   ADMIN
=============================== */
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest admin
    Route::get('/login',   [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected
    Route::middleware('auth.admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Verifikasi
        Route::prefix('verifications')->name('verification.')->group(function () {
            Route::get('/',                        [AdminVerificationController::class, 'index'])->name('index');
            Route::get('/{verification}',          [AdminVerificationController::class, 'show'])->name('show');
            Route::patch('/{verification}/take',   [AdminVerificationController::class, 'take'])->name('take');
            Route::patch('/{verification}/decide', [AdminVerificationController::class, 'decide'])->name('decide');
        });

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/artists', [AdminUserController::class, 'artists'])->name('artists');
            Route::get('/clients', [AdminUserController::class, 'clients'])->name('clients');
        });
        Route::get('/home-setting',   [AdminHomeSettingController::class, 'edit'])->name('home-setting.edit');
        Route::patch('/home-setting', [AdminHomeSettingController::class, 'update'])->name('home-setting.update');
        Route::delete('/home-setting/image', [AdminHomeSettingController::class, 'removeImage'])->name('home-setting.remove-image');
    });
});

/* ===============================
   VERIFIKASI PORTFOLIO ARTIST
=============================== */
Route::middleware('auth')->prefix('verification')->name('verification.')->group(function () {
    Route::get('/create',  [VerificationController::class, 'create'])->name('create');
    Route::post('/store',  [VerificationController::class, 'store'])->name('store');
    Route::get('/status',  [VerificationController::class, 'status'])->name('status');
});

/* ===============================
   ARTWORK
=============================== */
Route::get('/artwork/{id}',    [ArtworkController::class, 'show'])->name('artwork.detail');
Route::post('/artwork/update', [ArtworkController::class, 'updateFromModal'])->name('artwork.update');
Route::post('/artwork/delete', [ArtworkController::class, 'destroyFromModal'])->name('artwork.delete');

/* ===============================
   PROFILE & DASHBOARD
=============================== */
Route::middleware('auth')->group(function () {
    Route::get('/profile/{id}',        [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/{id}/update', [ProfileController::class, 'update'])->name('profile.update-popup');
});

Route::get('/dashboard', [ArtistDashboardController::class, 'index'])
    ->name('artist.dashboard')
    ->middleware('auth');

/* ===============================
   FOLLOW
=============================== */
Route::post('/follow/{id}', [FollowController::class, 'toggle'])
    ->middleware('auth')
    ->name('follow.toggle');

/* ===============================
   UPLOAD
=============================== */
Route::get('/upload',             [UploadController::class, 'popup'])->name('upload.popup');
Route::post('/upload-artwork',    [UploadController::class, 'uploadArtwork'])->name('upload.artwork');
Route::post('/upload-commission', [UploadController::class, 'uploadCommission'])->name('upload.commission');

/* ===============================
   COMMISSION SERVICE
=============================== */
Route::get('/commission/{id}',    [CommissionServiceController::class, 'show'])->name('commission.show');
Route::put('/commission/{id}',    [CommissionServiceController::class, 'update'])->name('commission.update');
Route::delete('/commission/{id}', [CommissionServiceController::class, 'destroy'])->name('commission.delete');

/* ===============================
   ORDER
=============================== */
Route::middleware('auth')->group(function () {
    Route::post('/order',                      [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{orderId}',             [OrderController::class, 'detail'])->name('order.detail');
    Route::patch('/order/{orderId}/brief',     [OrderController::class, 'updateBrief'])->name('order.updateBrief');
    Route::get('/cart',                        [OrderController::class, 'cart'])->name('cart.index');
    Route::post('/order/accept',               [OrderController::class, 'accept'])->name('order.accept');
    Route::post('/order/reject',               [OrderController::class, 'reject'])->name('order.reject');
    Route::post('/order/send',                 [OrderController::class, 'sendResult'])->name('order.send');
    Route::post('/order/revision',             [OrderController::class, 'revision'])->name('order.revision');
    Route::post('/order/complete',             [OrderController::class, 'complete'])->name('order.complete');
    Route::post('/order/client-cancel',        [OrderController::class, 'clientCancel'])->name('order.clientCancel');
    Route::post('/order/request-refund',       [OrderController::class, 'requestRefund'])->name('order.requestRefund');
    Route::post('/order/request-extension',    [OrderController::class, 'requestExtension'])->name('order.requestExtension');
    Route::post('/order/respond-extension',    [OrderController::class, 'respondExtension'])->name('order.respondExtension');
});

/* ===============================
   PAYMENT — Midtrans
=============================== */
Route::middleware('auth')->group(function () {
    Route::post('/payment/checkout',      [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/verify',        [PaymentController::class, 'verify'])->name('payment.verify');
    Route::get('/payment/success',        [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/unfinish',       [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error',          [PaymentController::class, 'error'])->name('payment.error');
    Route::get('/payment/finish',         [PaymentController::class, 'finish'])->name('payment.finish');
    Route::post('/payment/verify-status', [PaymentController::class, 'verifyStatus'])->name('payment.verifyStatus');
});

// Webhook Midtrans — tanpa auth & CSRF
Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->name('payment.notification');

/* ===============================
   CHAT
=============================== */
Route::middleware('auth')->group(function () {
    Route::get('/chat',                 [ChatController::class, 'list'])->name('chat.list');
    Route::get('/chat/thread',          [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send',           [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/fetch',           [ChatController::class, 'fetch'])->name('chat.fetch');
    Route::get('/chat/active-channels', [ChatController::class, 'activeChannels']);
    Route::get('/chat/unread-count',    [ChatController::class, 'unreadCount']);
});

/* ===============================
   LIKES
=============================== */
Route::middleware('auth')->group(function () {
    Route::post('/like', [LikeController::class, 'toggle'])->name('like.toggle');
});

/* ===============================
   REVIEWS
=============================== */
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('review.store');
});

/* ===============================
   NOTIFICATIONS
=============================== */
Route::middleware('auth')->group(function () {
    Route::get('/notifications',                 [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',       [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{notifId}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/count',           [NotificationController::class, 'unreadCount'])->name('notifications.count');
});

/* ===============================
   FALLBACK
=============================== */
Route::fallback(fn() => redirect()->route('home'));
