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
    Schema::create('payment_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->string('gateway'); // Phân biệt cổng: 'momo', 'cod', 'vnpay',...
        $table->string('gateway_order_id')->nullable()->index(); // ID đơn hàng phía cổng thanh toán
        $table->string('transaction_id')->nullable()->index(); // Mã giao dịch của cổng thanh toán
        $table->decimal('amount', 15, 2); // Số tiền thanh toán
        $table->string('status')->default('pending'); // Trạng thái: pending, paid, failed, canceled,...
        $table->integer('result_code')->nullable(); // Mã kết quả từ cổng thanh toán trả về
        $table->string('message')->nullable(); // Thông báo kết quả từ cổng thanh toán
        $table->json('request_payload')->nullable(); // Dữ liệu gửi sang cổng thanh toán
        $table->json('response_payload')->nullable(); // Dữ liệu cổng thanh toán phản hồi lại
        $table->timestamp('paid_at')->nullable(); // Thời gian thanh toán thành công
        $table->timestamps();

        // Đảm bảo không trùng lặp mã đơn của cùng 1 cổng thanh toán
        $table->unique(['gateway', 'gateway_order_id']);

        // Đánh index để tăng tốc độ truy vấn theo order_id và status
        $table->index(['order_id', 'status']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
