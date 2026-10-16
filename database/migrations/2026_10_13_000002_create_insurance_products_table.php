<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insurance_products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('provider');
            $table->string('name');

            // health | life | work | education
            $table->string('category')->index();

            $table->decimal('monthly_premium', 15, 2)->default(0);
            $table->decimal('annual_premium', 15, 2)->default(0);
            $table->decimal('coverage_limit', 15, 2)->default(0);

            // Array of bilingual text: [['id' => '...', 'en' => '...']]
            $table->json('benefits');
            $table->json('exclusions');
            $table->json('claim_documents');

            $table->string('waiting_period')->nullable();
            $table->string('coverage_period')->nullable();
            $table->text('claim_process')->nullable();

            // Data transparency: clearly mark when a product is simulated data.
            $table->boolean('is_sample')->default(true);
            $table->string('data_source')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_products');
    }
};
