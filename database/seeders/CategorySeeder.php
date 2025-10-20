<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Untuk menyimpan ID kategori Level 1
        $parentIds = []; 

        // --- LEVEL 1: Kategori Utama (3 Kategori) ---
        $level1Categories = [
            'Elektronik & Gadget',
            'Fashion Pria',
            'Peralatan Rumah Tangga',
        ];

        foreach ($level1Categories as $index => $name) {
            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'parent_id' => null, // Ini adalah root
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
            $parentIds[] = $category->id;
        }

        // --- LEVEL 2: Subkategori (6 Kategori) ---
        // Subkategori untuk Elektronik (parent_id[0])
        Category::create(['name' => 'Smartphone', 'slug' => 'smartphone', 'parent_id' => $parentIds[0], 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Laptop & Komputer', 'slug' => 'laptop-komputer', 'parent_id' => $parentIds[0], 'is_active' => true, 'sort_order' => 2]);
        
        // Subkategori untuk Fashion Pria (parent_id[1])
        Category::create(['name' => 'Pakaian Atas', 'slug' => 'pakaian-atas-pria', 'parent_id' => $parentIds[1], 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Aksesoris Pria', 'slug' => 'aksesoris-pria', 'parent_id' => $parentIds[1], 'is_active' => true, 'sort_order' => 2]);

        // Subkategori untuk Rumah Tangga (parent_id[2])
        Category::create(['name' => 'Dapur', 'slug' => 'peralatan-dapur', 'parent_id' => $parentIds[2], 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Kamar Tidur', 'slug' => 'perlengkapan-kamar-tidur', 'parent_id' => $parentIds[2], 'is_active' => true, 'sort_order' => 2]);


        // --- LEVEL 3: Sub-subkategori (3 Kategori) ---
        // Kita ambil parent ID dari Subkategori 'Smartphone' (Anggap ID 4) dan 'Laptop' (ID 5).
        // CATATAN: Karena ID bertambah sekuensial, kita bisa prediksi ID-nya:
        // Level 1: ID 1, 2, 3
        // Level 2: ID 4, 5, 6, 7, 8, 9
        // Parent ID 4 (Smartphone)
        Category::create(['name' => 'Android', 'slug' => 'android', 'parent_id' => 4, 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'iOS', 'slug' => 'ios', 'parent_id' => 4, 'is_active' => true, 'sort_order' => 2]);
        
        // Parent ID 5 (Laptop & Komputer)
        Category::create(['name' => 'PC Gaming', 'slug' => 'pc-gaming', 'parent_id' => 5, 'is_active' => true, 'sort_order' => 1]);

        // Total: 3 (L1) + 6 (L2) + 3 (L3) = 12 Kategori
    }
}