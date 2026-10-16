<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Policy documents uploaded by users and their processing trails.
 *
 * Tracked status flow (see brief section 4E):
 *   uploaded → validated → extracting → extracted → analysing → analysed
 * and branches to `failed` if any stage fails. A `failed` status MUST NOT
 * become successful just because the document is reopened.
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

            // AI analysis summary (both languages) and references actually
            // found. Do not populate this with fabricated references.
            $table->json('summary')->nullable();
            $table->json('references')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamp('review_requested_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // Processing stages ("Processing History" tab).
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
