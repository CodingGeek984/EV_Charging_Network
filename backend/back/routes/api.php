<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StationController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ChargingSessionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'auth'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    Route::get('/stations', [StationController::class, 'index'])->name('stations.index');

    Route::post('/charging/start', [ChargingSessionController::class, 'start'])->name('charging.start');
    Route::post('/charging/{id}/stop', [ChargingSessionController::class, 'stop'])->name('charging.stop');

    Route::post('/wallet/topup', [WalletController::class, 'store'])->name('wallet.topup');
});