<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- PASTIKAN DB FACADE DIIMPORT

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        // NONAKTIFKAN Foreign Key Checks SEMENTARA
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 

        Product::truncate(); // <-- BARIS INI YANG MEMICU ERROR

        // AKTIFKAN kembali Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

        // Buat 80 produk acak
        Product::factory()
            ->count(80)
            ->create();
    }
}
