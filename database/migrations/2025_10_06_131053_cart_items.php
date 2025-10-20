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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // dua mode identitas keranjang:
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // login
            $table->uuid('session_id')->nullable()->index(); // guest (SPA)

            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            $table->unsignedInteger('qty');
            $table->unsignedInteger('price_each');

            $table->string('product_name_snapshot')->nullable();
            $table->string('sku_snapshot')->nullable();
            $table->unsignedInteger('weight_snapshot')->default(0);

            $table->enum('status', ['in_cart', 'checked_out', 'saved', 'removed'])->default('in_cart');

            $table->timestamps();

            // Query cepat
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'product_id', 'status']);
            $table->index(['session_id', 'status']);
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
