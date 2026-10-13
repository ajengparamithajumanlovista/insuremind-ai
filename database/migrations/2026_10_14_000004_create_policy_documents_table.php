<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dokumen polis yang diunggah pengguna beserta jejak pemrosesannya.
 *
 * Alur status yang dilacak (lihat brief bagian 4E):
 *   uploaded → validated → extracting → extracted → analysing → analysed
 * dan bercabang ke `failed` bila salah satu tahap gagal. Status `failed`
 * TIDAK boleh berubah menjadi sukses hanya karena dokumen dibuka kembali.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('disk', 40)->default('local');
            $table->string('path');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('category', 20)->nullable()->index();

            $table->string('status', 30)->default('uploaded')->index();
            $table->string('extraction_status', 30)->default('pending')->index();
            $table->string('analysis_status', 30)->default('pending')->index();

            // Ringkasan hasil analisis AI (dua bahasa) dan referensi yang
            // benar-benar ditemukan. Tidak boleh diisi referensi fiktif.
            $table->json('summary')->nullable();
            $table->json('references')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamp('review_requested_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Tahapan pemrosesan (tab "Riwayat Pemrosesan").
        Schema::create('document_processing_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_document_id')->constrained('policy_documents')->cascadeOnDelete();
            $table->string('stage', 40);
            $table->string('status', 20);
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['policy_document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_processing_events');
        Schema::dropIfExists('policy_documents');
    }
};
