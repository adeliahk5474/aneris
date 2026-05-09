<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\CommissionServiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FollowController;

// ===============================
// HOME / HOMEPAGE
// ===============================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ===============================
// EXPLORE / SEARCH PAGE
// ===============================
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

// DETAIL ARTWORK (dari explore/home)
Route::get('/artwork/{id}', [ArtworkController::class, 'show'])->name('artwork.detail');

// ===============================
// AUTH
// ===============================
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth.form');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/auth/login', [AuthController::class, 'showAuthForm'])->name('login');


// ===============================
// DASHBOARD PROFILE
// ===============================

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/{id}/update', [ProfileController::class, 'update'])->name('profile.update-popup');
});
Route::get('/dashboard', [ArtistDashboardController::class, 'index'])
    ->name('artist.dashboard')
    ->middleware('auth');

Route::post('/follow/{id}', [FollowController::class, 'toggle'])
    ->middleware('auth')
    ->name('follow.toggle');

// ===============================
// COMMISSION
// ===============================
Route::get('/upload', [UploadController::class, 'popup'])->name('upload.popup');
Route::post('/upload-artwork', [UploadController::class, 'uploadArtwork'])->name('upload.artwork');
Route::post('/artwork/update', [ArtworkController::class, 'updateFromModal'])->name('artwork.update');
Route::post('/artwork/delete', [ArtworkController::class, 'destroyFromModal'])->name('artwork.delete');
// API: create a commission/order from client to artist
Route::post('/commission', [CommissionController::class, 'store'])->name('commission.store')->middleware('auth');
Route::post('/upload-commission', [UploadController::class, 'uploadCommission'])->name('upload.commission');
Route::get('/commission/{id}', [CommissionServiceController::class, 'detail'])
    ->name('commission.detail');
Route::get('/commission/{id}', [CommissionServiceController::class, 'show'])
    ->name('commission.show');
Route::put(
    '/commission/{id}',
    [CommissionServiceController::class, 'update']
)
    ->name('commission.update');
Route::delete('/commission/{id}', [CommissionServiceController::class, 'destroy'])
    ->name('commission.delete');
Route::post('/order', [OrderController::class, 'store'])
    ->name('order.store');
Route::post('/order/pay', [OrderController::class, 'pay'])->name('order.pay');
// ===============================
// CART
// ===============================
Route::get('/cart', [OrderController::class, 'cart'])
    ->middleware('auth')
    ->name('cart.index');
Route::post('/order/accept', [OrderController::class, 'accept'])->name('order.accept');
Route::post('/order/reject', [OrderController::class, 'reject'])->name('order.reject');
Route::post('/order/revision', [OrderController::class, 'revision'])
    ->name('order.revision');
Route::post('/order/complete', [OrderController::class, 'complete'])->name('order.complete');

Route::middleware('auth')->group(function () {

    Route::get('/chat', [ChatController::class, 'list'])->name('chat.list');

    Route::get('/chat/thread', [ChatController::class, 'index'])->name('chat.index');

    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

    Route::get('/chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');
});

Route::get('/notifications', function () {
    return view('pages.notifications');
})->name('notifications')->middleware('auth');

Route::get('/commission/cart', function () {
    $hideBottomNavbar = true;
    return view('pages.commission_cart');
})->name('commission.cart')->middleware('auth');

Route::get('/service/{id}', function ($id) {
    return view('pages.commission_service', ['id' => $id]);
})->name('service.detail');

Route::get('/orders', function () {
    return view('pages.client_orders');
})->name('client.orders')->middleware('auth');

Route::get('/profile/{id}/reviews', function ($id) {
    return view('pages.artist_reviews', ['id' => $id]);
})->name('artist.reviews')->middleware('auth');


// ===============================
// FALLBACK
// ===============================
Route::fallback(fn() => redirect()->route('home'));
