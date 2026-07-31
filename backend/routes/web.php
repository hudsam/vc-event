<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

Route::middleware(\App\Http\Middleware\AdminAuth::class)->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::patch('/events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
    Route::patch('/events/{event}/archive', [EventController::class, 'archive'])->name('events.archive');
    Route::patch('/events/{event}/draft', [EventController::class, 'draft'])->name('events.draft');
    
    Route::resource('events', EventController::class);
});
