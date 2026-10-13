<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Umpan balik pengguna atas jawaban AI.
 *
 * Data ini menjadi sinyal masuknya kasus ke AI Quality Review. Feedback
 * negatif BUKAN bukti bahwa jawaban salah (lihat brief bagian 5F), hanya
 * penanda agar diperiksa lebih lanjut.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('sentiment', 10);
            $table->string('reason', 60)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['chat_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_feedbacks');
    }
};
