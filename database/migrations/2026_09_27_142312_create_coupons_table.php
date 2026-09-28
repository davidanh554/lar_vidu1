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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->enum('type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('value', 12, 2);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->decimal('min_order_value', 12, 2)->default(0);
            $table->integer('quantity')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
