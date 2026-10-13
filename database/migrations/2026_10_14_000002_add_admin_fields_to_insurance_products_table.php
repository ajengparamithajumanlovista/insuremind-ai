<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom katalog untuk pengelolaan produk pada Admin Dashboard.
 *
 * - is_active       : produk nonaktif tetap menyimpan riwayatnya, tetapi
 *                     tidak lagi direkomendasikan sebagai produk aktif.
 * - description     : deskripsi produk (dua bahasa disimpan sebagai JSON
 *                     pasangan id/en, konsisten dengan kolom `benefits`).
 * - entry_age_min/max: rentang usia masuk bila berlaku untuk kategori tsb.
 * - source_url      : tautan sumber resmi agar informasi dapat diverifikasi.
 *
 * Catatan: migrasi ini bersifat ADITIF. Tidak ada kolom lama yang diubah
 * atau dihapus sehingga User Dashboard yang sudah berjalan tetap aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance_products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index()->after('category');
            $table->json('description')->nullable()->after('name');
            $table->unsignedTinyInteger('entry_age_min')->nullable()->after('coverage_period');
            $table->unsignedTinyInteger('entry_age_max')->nullable()->after('entry_age_min');
            $table->string('source_url', 500)->nullable()->after('data_source');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_products', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'description', 'entry_age_min', 'entry_age_max', 'source_url']);
        });
    }
};
