<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Pindahkan konstanta ke DALAM kelas
    private const CATEGORY_PARENT_FK_NAME = 'fk_cat_parent_category';

    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('parent_id', self::CATEGORY_PARENT_FK_NAME)
                  ->references('id')
                  ->on('categories')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Hapus Foreign Key menggunakan NAMA EKSPLISIT
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(self::CATEGORY_PARENT_FK_NAME);
        });

        Schema::dropIfExists('categories');
    }
};