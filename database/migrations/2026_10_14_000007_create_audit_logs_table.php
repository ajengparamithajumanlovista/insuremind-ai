<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit Log — a trail of important activity that must not be changed arbitrarily.
 *
 * This table is designed to be append-only: it has no `updated_at` column and
 * no update/delete endpoint for log rows. Corrections are made by writing a
 * new row (see the `corrects_log_id` column).
 *
 * The `before`/`after` columns store only safe metadata. Passwords,
 * authentication tokens, full policy contents, and sensitive conversations
 * MUST NOT be stored here; AuditLogger uses a denylist before writing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();

            // Actor: may be a real user or a system process (nullable).
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_role', 20)->default('system');
            $table->string('actor_name')->nullable();

            $table->string('category', 40)->index();
            $table->string('action', 60)->index();
            $table->string('description', 500);

            // The affected object uses a consistent identity across pages.
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

            // Only created_at — the log is append-only.
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
