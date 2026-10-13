<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit Log — jejak aktivitas penting yang tidak boleh diubah sembarangan.
 *
 * Tabel ini dirancang append-only: tidak ada kolom `updated_at` dan tidak
 * akan ada endpoint update/delete untuk baris log. Koreksi dilakukan dengan
 * menulis baris baru (lihat kolom `corrects_log_id`).
 *
 * Kolom `before`/`after` hanya menyimpan metadata yang aman. Kata sandi,
 * token autentikasi, isi polis lengkap, dan percakapan sensitif DILARANG
 * disimpan di sini; AuditLogger memakai daftar merah sebelum menulis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();

            // Pelaku: bisa pengguna nyata, bisa proses sistem (nullable).
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_role', 20)->default('system');
            $table->string('actor_name')->nullable();

            $table->string('category', 40)->index();
            $table->string('action', 60)->index();
            $table->string('description', 500);

            // Objek terdampak memakai identitas yang konsisten antar halaman.
            $table->string('subject_type', 60)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label')->nullable();

            $table->string('result', 20)->default('success');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('reason', 500)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('request_id', 64)->nullable()->index();

            $table->foreignId('corrects_log_id')->nullable()->constrained('audit_logs')->nullOnDelete();

            // Hanya created_at — log bersifat append-only.
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
