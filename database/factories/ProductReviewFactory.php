<?php

namespace Database\Factories;

use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductReview::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement([
            ProductReview::STATUS_APPROVED,
            ProductReview::STATUS_APPROVED, // 80% kemungkinan approved
            ProductReview::STATUS_APPROVED,
            ProductReview::STATUS_PENDING,
            ProductReview::STATUS_REJECTED
        ]);

        $rating = $this->faker->numberBetween(1, 5);
        $title = $this->faker->sentence(rand(3, 7));

        return [
            // product_id dan user_id akan diisi di seeder
            'product_id' => Product::inRandomOrder()->first()->id ?? 1,
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            // order_item_id diabaikan untuk seeding cepat, is_verified_purchase random
            'order_item_id' => null, 
            
            'rating' => $rating,
            'title' => Str::limit($title, 80),
            'body' => $this->faker->realText(rand(100, 300)),
            
            'is_verified_purchase' => $this->faker->boolean(70), // 70% verified
            'status' => $status,
            
            'approved_at' => ($status === ProductReview::STATUS_APPROVED) ? now() : null,
            'rejected_at' => ($status === ProductReview::STATUS_REJECTED) ? now() : null,
            'rejected_reason' => ($status === ProductReview::STATUS_REJECTED) ? 'Does not meet guideline standards.' : null,

            'helpful_count' => 0, // Akan dihitung oleh ReviewVote model hook
            'report_count' => $this->faker->numberBetween(0, 5),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
