<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LifeSimulatorController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiskAssessmentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

// Otentikasi (satu formulir yang mendeteksi peran admin vs pengguna secara otomatis).
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Area yang memerlukan otentikasi.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/analisis-risiko', [RiskAssessmentController::class, 'index'])->name('risk.index');
    Route::post('/analisis-risiko', [RiskAssessmentController::class, 'store'])->name('risk.store');

    Route::get('/simulator-risiko', [LifeSimulatorController::class, 'index'])->name('simulator.index');
    Route::post('/simulator-risiko', [LifeSimulatorController::class, 'store'])->name('simulator.store');

    Route::get('/perbandingan', [ComparisonController::class, 'index'])->name('comparison.index');

    Route::get('/kategori/{category}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('/polis', [PolicyController::class, 'index'])->name('policies.index');
    Route::post('/polis', [PolicyController::class, 'store'])->name('policies.store');
    Route::put('/polis/{policy}', [PolicyController::class, 'update'])->name('policies.update');
    Route::delete('/polis/{policy}', [PolicyController::class, 'destroy'])->name('policies.destroy');

    Route::get('/asisten', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/asisten', [ChatController::class, 'store'])->name('chat.store');
});

