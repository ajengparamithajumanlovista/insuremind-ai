<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

// Authentication (single form auto-detects admin vs user role).
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated area.
Route::view('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

