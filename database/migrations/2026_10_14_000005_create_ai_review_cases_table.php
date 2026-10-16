<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kasus AI Quality Review.
 *
 * One case represents ONE assistant answer that needs human review.
 * One answer can trigger multiple reasons (missing references, negative
 * feedback, questionable references), so statistics are based on UNIQUE
 * CASES, not the number of events.
 *
 * Cases can also originate from processes other than chat answers (e.g. failed
 * document analysis), so `chat_message_id` and `policy_document_id` are both
 * nullable, with only one populated.
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
            // Primary issue type and full list to prevent duplicate cases.
            $table->string('issue_type', 40)->index();
            $table->json('issues')->nullable();

            // Snapshot the question and answer so history remains intact even
            // if the user deletes the original conversation.
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

        // Review history: record every reviewer decision, not just the final
        // status, so the audit trail remains intact.
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
