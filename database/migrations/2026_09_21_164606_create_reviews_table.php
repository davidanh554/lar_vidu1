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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5); // 1 - 5 sao
            $table->string('color')->nullable(); // Phân loại hàng (màu sắc)
            $table->string('match_description')->nullable(); // Đúng với mô tả
            $table->string('quality_rating')->nullable(); // Chất lượng sản phẩm
            $table->text('comment')->nullable(); // Nội dung nhận xét
            $table->unsignedInteger('helpful_count')->default(0); // Số lượt thấy hữu ích
            $table->timestamps();

            $table->index(['product_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
