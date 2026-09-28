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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            $table->string('image')->nullable()->after('slug');
            $table->string('connectivity')->nullable()->after('storage');
            $table->string('color')->nullable()->after('connectivity');
            $table->boolean('is_active')->default(true)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['category_id', 'brand_id', 'image', 'connectivity', 'color', 'is_active']);
        });
    }
};
