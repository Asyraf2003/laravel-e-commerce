<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();

            // dua mode identitas: user (login) atau guest (session)
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->uuid('session_id')->nullable()->index();

            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Cegah item duplikat
            $table->unique(['user_id', 'product_id']);
            $table->unique(['session_id', 'product_id']);

            // Query cepat
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('wishlists');
    }
};
