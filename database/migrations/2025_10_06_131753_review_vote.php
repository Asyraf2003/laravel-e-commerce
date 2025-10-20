<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('review_id')->constrained('product_reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('vote', ['helpful','not_helpful'])->default('helpful');

            $table->timestamps();

            $table->unique(['review_id','user_id']); // satu suara per user
            $table->index(['review_id', 'vote']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('review_votes');
    }
};
