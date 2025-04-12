<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

// Root route with auth check
Route::get('/', function () {
    if (auth()->check()) {
        switch (auth()->user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'customer':
                return redirect()->route('dashboard');
            case 'agent':
                return redirect()->route('agent.dashboard');
            default:
                return redirect()->route('login');
        }
    }
    return redirect('/login');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/customers/create', [AdminController::class, 'create'])->name('customers.create');
    Route::post('/customers', [AdminController::class, 'store'])->name('customers.store');
    Route::get('/customers/{user}/edit', [AdminController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{user}', [AdminController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{user}', [AdminController::class, 'destroy'])->name('customers.destroy');
    Route::put('/customers/{user}/usage', [AdminController::class, 'updateUsage'])->name('customers.updateUsage');
    
    Route::get('/agents', [AdminController::class, 'agents'])->name('admin.agents');
    Route::get('/agents/create', [AdminController::class, 'createAgent'])->name('agents.create');
    Route::post('/agents', [AdminController::class, 'storeAgent'])->name('agents.store');
    Route::get('/agents/{user}/edit', [AdminController::class, 'editAgent'])->name('agents.edit');
    Route::put('/agents/{user}', [AdminController::class, 'updateAgent'])->name('agents.update');
    Route::delete('/agents/{user}', [AdminController::class, 'destroyAgent'])->name('agents.destroy');
    
    Route::get('/meter-readings', [AdminController::class, 'meterReadings'])->name('admin.meter_readings');
    Route::put('/meter-readings/{reading}', [AdminController::class, 'updateMeterReading'])->name('admin.update_meter_reading');
    Route::get('/usage-history', [AdminController::class, 'usageHistory'])->name('admin.usage_history');

    // Customer Profile Management
    Route::get('/customers/{user}/profile', [AdminController::class, 'viewCustomerProfile'])->name('admin.customers.profile');
    Route::put('/customers/{user}/profile', [AdminController::class, 'updateCustomerProfile'])->name('admin.customers.profile.update');
    Route::put('/customers/{user}/toggle-status', [AdminController::class, 'toggleCustomerStatus'])->name('admin.customers.toggle-status');
    Route::delete('/customers/{user}/delete', [AdminController::class, 'deleteCustomer'])->name('admin.customers.delete');
});

// Customer Routes (No prefix, using default dashboard)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::post('/pay', [CustomerController::class, 'pay'])->name('customer.pay');
});

// Agent Routes
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [AgentController::class, 'customers'])->name('customers');
    Route::get('/meter-readings', [AgentController::class, 'meterReadings'])->name('meter_readings');
    Route::get('/customers/{customer}/readings', [AgentController::class, 'getCustomerReadings'])->name('customer.readings');
    Route::post('/submit-reading', [AgentController::class, 'submitReading'])->name('submit_reading');
});

// Profile Routes (Shared across all roles)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});

// MPesa Callback Route (Public, no auth required)
Route::post('/mpesa/callback', [CustomerController::class, 'callback'])->name('mpesa.callback');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [CustomAuthenticatedSessionController::class, 'store']);
});
Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Messaging routes
Route::middleware('auth')->group(function () {
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/mark-as-read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark_as_read');
});