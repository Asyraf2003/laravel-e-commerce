<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user spesifik (Admin dan Default User)
        
        // Admin Utama (menggunakan explicit data)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Main Administrator',
                'password' => Hash::make('12345678'),
                'role' => Role::ADMIN, 
            ]
        );

        // Regular User Utama (menggunakan explicit data)
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('12345678'),
                'role' => Role::USER,
            ]
        );

        // 2. Buat 30 User acak menggunakan Factory
        
        // Catatan: factory akan menggunakan password 'password'
        User::factory()
            ->count(30) // Tentukan jumlah user acak yang diinginkan
            ->create();

        // (Opsional) Jika Anda ingin membuat beberapa user acak lainnya sebagai Admin, Anda bisa gunakan:
        // User::factory(5)->admin()->create(); 
    }
}