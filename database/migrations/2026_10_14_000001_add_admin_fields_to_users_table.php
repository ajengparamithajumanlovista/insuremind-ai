<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom tambahan untuk Manajemen Pengguna pada Admin Dashboard.
 *
 * - status          : active | inactive | suspended (menonaktifkan akun
 *                     TIDAK menghapus riwayat aktivitasnya).
 * - phone           : opsional, dipakai pada formulir tambah pengguna.
 * - last_activity_at: dipakai untuk kolom "Aktivitas Terakhir" (bukan
 *                     berarti pengguna sedang online).
 * - admin_note      : catatan internal admin, tidak pernah ditampilkan
 *                     kepada pemilik akun.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->index()->after('role');
            $table->string('phone', 30)->nullable()->after('status');
            $table->timestamp('last_activity_at')->nullable()->after('phone');
            $table->text('admin_note')->nullable()->after('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'phone', 'last_activity_at', 'admin_note']);
        });
    }
};
