<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\ClientDashboardController;


Route::get('/auth', [AuthController::class, 'showForm'])->name('auth.form');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Dashboard
Route::get('/dashboard/client', function() {
    return 'Dashboard Client';
});
Route::get('/dashboard/artist', function() {
    return 'Dashboard Artist';
});
