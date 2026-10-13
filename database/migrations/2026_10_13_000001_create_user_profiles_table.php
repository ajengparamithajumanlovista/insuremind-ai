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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Profil dasar
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('occupation')->nullable();
            $table->string('city')->nullable();

            // Kondisi finansial (disimpan dalam Rupiah)
            $table->decimal('monthly_income', 15, 2)->default(0);
            $table->decimal('monthly_expense', 15, 2)->default(0);
            $table->decimal('monthly_debt', 15, 2)->default(0);
            $table->decimal('savings', 15, 2)->default(0);
            $table->unsignedTinyInteger('dependents')->default(0);

            // Perlindungan yang sudah dimiliki
            $table->boolean('has_health_insurance')->default(false);
            $table->boolean('has_life_insurance')->default(false);
            $table->boolean('has_work_insurance')->default(false);
            $table->decimal('existing_coverage', 15, 2)->default(0);

            // Rencana pendidikan anak
            $table->unsignedTinyInteger('education_years')->default(0);
            $table->decimal('education_cost', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
