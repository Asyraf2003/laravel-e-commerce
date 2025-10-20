<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pastikan ada kategori yang tersedia untuk dihubungkan
        $categoryIds = Category::pluck('id')->all();
        $name = $this->faker->words(3, true);

        // Atur ulasan
        $reviewsCount = $this->faker->numberBetween(0, 150);
        $reviewsAvg = $reviewsCount > 5 ? $this->faker->randomFloat(2, 3.5, 5.0) : 0.0;
        
        // Atur harga
        $originalPrice = $this->faker->numberBetween(1000, 10000) * 1000; // Harga kelipatan 1000 (1 Juta - 10 Juta)
        $discountPercent = $this->faker->randomElement([0, 0, 0, 5, 10, 20]); // 50% chance of 0% discount

        return [
            'category_id' => $this->faker->randomElement($categoryIds),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(3),
            'sku' => $this->faker->unique()->bothify('PRO-####-??'),

            'short_desc' => $this->faker->sentence(10),
            'long_desc' => $this->faker->paragraphs(3, true),

            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            
            'reviews_count' => $reviewsCount,
            'reviews_avg' => $reviewsAvg,
            
            'stock' => $this->faker->numberBetween(0, 500), // Bisa 0 (Out of Stock)
            'weight' => $this->faker->numberBetween(100, 5000), // Grams

            'share_fb' => $this->faker->boolean(80),
            'share_x' => $this->faker->boolean(80),
            'share_wa' => $this->faker->boolean(80),
            
            'is_best_seller' => $this->faker->boolean(30),
            'is_new' => $this->faker->boolean(60),
            'is_hot' => $this->faker->boolean(20),
            'is_featured' => $this->faker->boolean(40),
            
            'is_active' => $this->faker->boolean(95), // 95% aktif
            'published_at' => $this->faker->optional(0.9)->dateTimeBetween('-6 months', 'now'), // 90% sudah dipublikasi
        ];
    }
}
