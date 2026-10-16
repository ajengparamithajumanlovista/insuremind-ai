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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('provider_name');
            $table->string('policy_name');
            $table->string('policy_number')->nullable();

            // health | life | work | education | other
            $table->string('category')->default('other')->index();

            $table->decimal('premium_amount', 15, 2)->default(0);
            $table->decimal('coverage_amount', 15, 2)->default(0);

            // monthly | quarterly | semiannual | annual
            $table->string('payment_period')->default('monthly');

            $table->date('start_date')->nullable();
            $table->date('next_due_date')->nullable();

            // How many days before the due date to display a reminder.
            $table->unsignedSmallInteger('reminder_days')->default(7);

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
