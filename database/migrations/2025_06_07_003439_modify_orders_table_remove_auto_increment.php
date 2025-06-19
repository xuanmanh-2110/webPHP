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
        // Tạm thời xóa foreign key constraint
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        
        // Loại bỏ auto_increment từ cột id
        DB::statement('ALTER TABLE orders MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        
        // Khôi phục foreign key constraint
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Khôi phục auto_increment
            $table->id()->change();
        });
    }
};
