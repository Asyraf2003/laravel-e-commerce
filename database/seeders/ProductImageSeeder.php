<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan produk sudah ada
        if (Product::count() === 0) {
            $this->call(ProductSeeder::class);
        }

        ProductImage::truncate(); // Bersihkan data lama

        $products = Product::all();

        // Iterasi melalui setiap produk
        foreach ($products as $product) {
            // Tentukan jumlah gambar: 3 sampai 5 gambar per produk
            $imageCount = rand(3, 5); 
            $imagesData = [];

            for ($i = 1; $i <= $imageCount; $i++) {
                // Buat instance ProductImageFactory
                $image = ProductImage::factory()->make([
                    'product_id' => $product->id,
                    'sort_order' => $i,
                    // Gambar pertama (i=1) selalu menjadi primary
                    'is_primary' => ($i === 1) ? true : false, 
                ]);

                $imagesData[] = $image->toArray();
            }

            // Gunakan insert untuk performa yang lebih baik
            ProductImage::insert($imagesData);
        }
    }
}
