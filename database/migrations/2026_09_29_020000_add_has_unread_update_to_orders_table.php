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
        if (!Schema::hasColumn('orders', 'has_unread_update')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('has_unread_update')->default(false)->after('shipping_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'has_unread_update')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('has_unread_update');
            });
        }
    }
};
