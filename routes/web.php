<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\HomeController; 

// ===============================
// HOME / HOMEPAGE
// ===============================
Route::get('/', [HomeController::class, 'index'])->name('home'); // ✅ tampilkan homepage utama

// ===============================
// AUTH
// ===============================
Route::get('/auth', [AuthController::class, 'showAuthForm'])->name('auth.form');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/auth/verify', [AuthController::class, 'verify'])->name('auth.verify');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

// logout diarahkan ke homepage
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// ===============================
// DASHBOARD (ARTIST & CLIENT)
// ===============================
Route::middleware(['auth'])->group(function () {

    // Artist Dashboard
    Route::get('/dashboard/artist', [ArtistDashboardController::class, 'index'])
        ->name('dashboard.artist'); // harus sama dengan yang dipakai AuthController

    // Artwork CRUD
    Route::post('/artist/artwork/store', [ArtistDashboardController::class, 'storeArtwork'])
        ->name('artist.artwork.store');
    Route::delete('/artist/artwork/{id}', [ArtistDashboardController::class, 'destroyArtwork'])
        ->name('artist.artwork.destroy');

    // Commission Service CRUD
    Route::post('/artist/service/store', [ArtistDashboardController::class, 'storeService'])
        ->name('artist.service.store');
    Route::delete('/artist/service/{id}', [ArtistDashboardController::class, 'destroyService'])
        ->name('artist.service.destroy');

    // Client Dashboard
    Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
        ->name('dashboard.client');

    // Client Banner Upload
    Route::post('/dashboard/client/banner/update', [ClientDashboardController::class, 'updateBanner'])
        ->name('client.banner.update');
});

// ===============================
// FALLBACK
// ===============================
Route::fallback(fn() => redirect()->route('home'));
