<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kasus AI Quality Review.
 *
 * Satu kasus mewakili SATU jawaban asisten yang perlu diperiksa manusia.
 * Satu jawaban dapat memicu beberapa alasan (tanpa referensi, feedback
 * negatif, referensi meragukan) sehingga statistik dihitung berdasarkan
 * KASUS UNIK, bukan jumlah kejadian.
 *
 * Kasus juga bisa berasal dari proses yang bukan jawaban chat (mis. analisis
 * dokumen gagal) sehingga `chat_message_id` dan `policy_document_id`
 * keduanya nullable dan hanya salah satu yang terisi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_review_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('chat_message_id')->nullable()->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('policy_document_id')->nullable()->constrained('policy_documents')->cascadeOnDelete();

            $table->string('category', 20)->nullable()->index();
            // Alasan tunggal utama + daftar lengkap agar tidak duplikat kasus.
            $table->string('issue_type', 40)->index();
            $table->json('issues')->nullable();

            // Snapshot pertanyaan & jawaban agar riwayat tetap utuh walau
            // percakapan asal dihapus pengguna.
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->json('references')->nullable();

            $table->string('status', 30)->default('pending')->index();
            $table->boolean('has_reference')->default(false);
            $table->unsignedTinyInteger('negative_feedback_count')->default(0);

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_decision', 30)->nullable();
            $table->text('review_note')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // Riwayat pemeriksaan: setiap keputusan reviewer dicatat, bukan
        // hanya status akhir, sehingga jejak audit tetap utuh.
        Schema::create('ai_review_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_review_case_id')->constrained('ai_review_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 30);
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['ai_review_case_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_review_actions');
        Schema::dropIfExists('ai_review_cases');
    }
};
