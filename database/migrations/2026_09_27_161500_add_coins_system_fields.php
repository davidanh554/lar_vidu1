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
            $table->unsignedInteger('daily_coins_limit')->default(10)->after('coins'); // Giới hạn số xu nhận tối đa 1 ngày
            $table->unsignedInteger('coins_earned_today')->default(0)->after('daily_coins_limit'); // Số xu đã nhận trong ngày
            $table->timestamp('last_coin_reset_at')->nullable()->after('coins_earned_today'); // Thời gian reset xu ngày mới
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('coins_used')->default(0)->after('discount_amount'); // Số xu đã sử dụng trừ vào đơn
            $table->decimal('coins_discount', 15, 2)->default(0)->after('coins_used'); // Số tiền giảm từ xu (1 xu = 1.000đ)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_coins_limit', 'coins_earned_today', 'last_coin_reset_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coins_used', 'coins_discount']);
        });
    }
};
