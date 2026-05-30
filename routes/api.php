<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;

Route::post('/midtrans-callback', [ProdukController::class, 'callback']);
