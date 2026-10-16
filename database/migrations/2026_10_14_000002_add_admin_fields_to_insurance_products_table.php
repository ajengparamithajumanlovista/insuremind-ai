<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catalog columns for product management in the Admin Dashboard.
 *
 * - is_active       : inactive products retain their history but are no longer
 *                     recommended as active products.
 * - description     : product description (both languages stored as a JSON
 *                     id/en pair, consistent with the `benefits` column).
 * - entry_age_min/max: entry age range, when applicable to the category.
 * - source_url      : official source link so information can be verified.
 *
 * Note: this migration is ADDITIVE. No existing columns are changed or
 * removed, so the existing User Dashboard remains unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance_products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index()->after('category');
            $table->json('description')->nullable()->after('name');
            $table->unsignedTinyInteger('entry_age_min')->nullable()->after('coverage_period');
            $table->unsignedTinyInteger('entry_age_max')->nullable()->after('entry_age_min');
            $table->string('source_url', 500)->nullable()->after('data_source');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_products', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'description', 'entry_age_min', 'entry_age_max', 'source_url']);
        });
    }
};
