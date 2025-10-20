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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('sku')->unique()->nullable();

            $table->text('short_desc')->nullable();
            $table->longText('long_desc')->nullable();

            $table->unsignedInteger('original_price');        // Rupiah (integer)
            $table->unsignedTinyInteger('discount_percent')->nullable();

            $table->unsignedInteger('reviews_count')->default(0);
            $table->decimal('reviews_avg', 3, 2)->default(0);

            $table->unsignedBigInteger('stock')->default(1);
            $table->unsignedInteger('weight')->default(0);

            $table->boolean('share_fb')->default(true);
            $table->boolean('share_x')->default(true);
            $table->boolean('share_wa')->default(true);

            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_hot')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['category_id']);
            $table->index(['is_best_seller','is_new','is_hot','is_featured']);
            $table->index(['is_active', 'published_at']); // → listing cepat
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
