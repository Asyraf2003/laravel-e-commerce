<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\Role; // Pastikan Anda mengimpor Enum Role
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tentukan peran (role) yang mungkin untuk user acak
        $randomRoles = [Role::USER, Role::OTHER]; 
        
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // Atur password default yang sama untuk semua factory user
            'remember_token' => Str::random(10),
            // Berikan peran USER atau OTHER secara acak
            'role' => $this->faker->randomElement($randomRoles), 
        ];
    }
    
    /**
     * State transformation untuk menjadikan user sebagai Admin.
     * Gunakan ini hanya untuk user spesifik, bukan untuk factory acak.
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => Role::ADMIN,
            ];
        });
    }
}