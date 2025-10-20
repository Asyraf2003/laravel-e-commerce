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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('order_no')->unique();

            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('province_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('subdistrict_name')->nullable();
            $table->enum('destination_level', ['district', 'subdistrict'])->default('district');

            $table->string('address');
            $table->string('postal_code', 10)->nullable();

            // Ongkir
            $table->string('courier', 20);
            $table->string('service', 50);
            $table->unsignedInteger('shipping_cost');
            $table->unsignedInteger('weight_total')->default(0);

            // Biaya
            $table->unsignedInteger('discount_total')->default(0);
            $table->unsignedInteger('tax_total')->default(0);
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('total');
            $table->string('currency', 10)->default('IDR');

            // Midtrans
            $table->string('payment_gateway')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->unsignedInteger('gross_amount')->nullable();
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('payment_token')->nullable()->index();
            $table->string('payment_redirect_url')->nullable();
            $table->json('midtrans_payload')->nullable();

            $table->string('status', 30)->default('draft')->index();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
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
