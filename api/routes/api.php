<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{idOrSlug}', [EventController::class, 'show']);
Route::post('/events', [EventController::class, 'store']);
Route::put('/events/{id}', [EventController::class, 'update']);
Route::delete('/events/{id}', [EventController::class, 'destroy']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/users/{id}', [AuthController::class, 'getUser']);
Route::get('/users/email/{email}', [AuthController::class, 'getUserByEmail']);


