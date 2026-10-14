<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewCaseController as AdminReviewCaseController;
use App\Http\Controllers\Admin\UserManagementController;
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

/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
|
| Semua rute di bawah hanya dapat diakses pengguna yang sudah masuk DAN
| akunnya aktif. Hak per-halaman ditegakkan oleh middleware `admin` /
| `reviewer` serta Gate pada masing-masing aksi, bukan sekadar tombol yang
| disembunyikan di antarmuka.
|
*/
Route::middleware(['auth', 'active'])->prefix('admin')->name('admin.')->group(function () {
    // Ringkasan operasional (admin).
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pengguna (admin).
    Route::middleware('admin')->group(function () {
        Route::get('pengguna', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('pengguna/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::put('pengguna/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::post('pengguna/{user}/aktifkan', [UserManagementController::class, 'activate'])->name('users.activate');
        Route::post('pengguna/{user}/tangguhkan', [UserManagementController::class, 'suspend'])->name('users.suspend');

        // Katalog Produk (admin).
        Route::get('produk', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('produk/tambah', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('produk', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('produk/{product}', [AdminProductController::class, 'show'])->name('products.show');
        Route::put('produk/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::post('produk/{product}/status', [AdminProductController::class, 'toggle'])->name('products.toggle');

        // Dokumen Polis (admin).
        Route::get('dokumen', [AdminDocumentController::class, 'index'])->name('documents.index');
        Route::get('dokumen/{document}', [AdminDocumentController::class, 'show'])->name('documents.show');
        Route::get('dokumen/{document}/unduh', [AdminDocumentController::class, 'download'])->name('documents.download');
    });

    // AI Quality Review (admin & reviewer). Otorisasi per-kasus ditegakkan
    // pada controller melalui Gate `review-case`.
    Route::middleware('reviewer')->group(function () {
        Route::get('tinjauan', [AdminReviewCaseController::class, 'index'])->name('review.index');
        Route::get('tinjauan/{case}', [AdminReviewCaseController::class, 'show'])->name('review.show');
        Route::post('tinjauan/{case}/klaim', [AdminReviewCaseController::class, 'claim'])->name('review.claim');
        Route::post('tinjauan/{case}/putuskan', [AdminReviewCaseController::class, 'decide'])->name('review.decide');
        Route::post('tinjauan/{case}/catatan', [AdminReviewCaseController::class, 'note'])->name('review.note');
    });

    // Audit Log (admin). Hanya baca dan ekspor; tidak ada update/hapus.
    Route::middleware('admin')->group(function () {
        Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('audit/ekspor', [AuditLogController::class, 'export'])->name('audit.export');
        Route::get('audit/{auditLog}', [AuditLogController::class, 'show'])->name('audit.show');
    });
});
