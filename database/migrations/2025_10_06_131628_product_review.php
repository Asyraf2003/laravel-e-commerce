<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Jika ingin memastikan benar-benar pembeli, tautkan ke order_items (opsional)
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();

            $table->unsignedTinyInteger('rating'); // 1..5
            $table->string('title')->nullable();
            $table->text('body')->nullable();

            $table->boolean('is_verified_purchase')->default(false)->index(); // set true saat ada order_item_id valid
            $table->enum('status', ['pending','approved','rejected'])->default('pending')->index();

            // Moderation metadata
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_reason')->nullable();

            // Interaksi (opsional, bisa diisi dari votes table)
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('report_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Satu review per user per product
            $table->unique(['user_id', 'product_id']);

            // Index buat listing & filter cepat
            $table->index(['product_id', 'status', 'created_at']);
            $table->index(['rating']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_reviews');
    }
};
