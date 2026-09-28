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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('daily_spins_limit')->default(1)->after('role');
            $table->integer('spins_left')->default(1)->after('daily_spins_limit');
            $table->timestamp('last_spin_reset_at')->nullable()->after('spins_left');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_spins_limit', 'spins_left', 'last_spin_reset_at']);
        });
    }
};
