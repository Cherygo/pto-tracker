<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PtoRequestController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', LogoutController::class)->name('logout');

    // Admin routes
    Route::patch('/users/{user}/promote', [UserController::class, 'promoteToManager'])->name('users.promote');

    // PTO Request routes
    Route::post('/pto-requests', [PtoRequestController::class, 'store'])->name('pto-requests.store');
    Route::patch('/pto-requests/{ptoRequest}/approve', [PtoRequestController::class, 'approve'])->name('pto-requests.approve');
    Route::patch('/pto-requests/{ptoRequest}/deny', [PtoRequestController::class, 'deny'])->name('pto-requests.deny');
});

require __DIR__.'/settings.php';
