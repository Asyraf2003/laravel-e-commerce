<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Asumsi jalur gambar dummy
        $imageNumber = $this->faker->numberBetween(1, 10);
        
        return [
            // product_id akan diisi oleh seeder (closure)
            'image_path' => "storage/img/product-{$imageNumber}.png",
            'is_primary' => false, // Default non-primary
            'sort_order' => 0, // Akan diisi sekuensial oleh seeder
            'alt_text' => $this->faker->sentence(4),
        ];
    }
}
