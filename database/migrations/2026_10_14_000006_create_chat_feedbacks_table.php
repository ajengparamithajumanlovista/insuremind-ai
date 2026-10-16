<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * User feedback on AI answers.
 *
 * This data is a signal for creating an AI Quality Review case. Negative
 * feedback is NOT proof that an answer is wrong (see brief section 5F); it is
 * only a signal for further review.
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
