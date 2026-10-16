<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Product catalog change history ("History" tab on product details).
 *
 * Each significant change (premium, benefits, status) is recorded with its old
 * and new values so it can be traced through the Audit Log.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_product_id')->constrained('insurance_products')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->string('field', 60)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('reason', 500)->nullable();
            $table->timestamps();

            $table->index(['insurance_product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_histories');
    }
};
