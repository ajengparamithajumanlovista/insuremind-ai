<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additional columns for User Management in the Admin Dashboard.
 *
 * - status          : active | inactive | suspended (deactivating an account
 *                     does NOT delete its activity history).
 * - phone           : optional, used in the add-user form.
 * - last_activity_at: used for the "Last Activity" column (does not mean
 *                     the user is currently online).
 * - admin_note      : internal admin note, never displayed
 *                     to the account owner.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->index()->after('role');
            $table->string('phone', 30)->nullable()->after('status');
            $table->timestamp('last_activity_at')->nullable()->after('phone');
            $table->text('admin_note')->nullable()->after('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'phone', 'last_activity_at', 'admin_note']);
        });
    }
};
