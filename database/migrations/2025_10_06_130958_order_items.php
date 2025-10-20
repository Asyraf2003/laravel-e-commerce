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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();

            $table->string('product_name_snapshot');
            $table->string('sku_snapshot')->nullable();
            $table->unsignedInteger('weight_snapshot')->default(0);

            $table->unsignedInteger('qty');
            $table->unsignedInteger('price_each');
            $table->unsignedInteger('line_total');

            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['product_id']); // opsional, bantu laporan
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
