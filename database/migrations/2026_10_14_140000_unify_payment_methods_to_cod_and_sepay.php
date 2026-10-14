<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chuyen doi toan bo transaction MoMo hoac khac sang sepay (VietQR)
        DB::table('payment_transactions')
            ->where('gateway', '!=', 'cod')
            ->update(['gateway' => 'sepay']);

        // Chuyen doi trang thai don hang paid_momo thanh paid
        DB::table('orders')
            ->where('status', 'paid_momo')
            ->update(['status' => 'paid']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
