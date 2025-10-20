<?php

namespace Database\Factories;

use App\Models\ReviewVote;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewVoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReviewVote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // review_id dan user_id akan diisi di seeder
            'vote' => $this->faker->randomElement(['helpful', 'helpful', 'not_helpful']), // Mayoritas helpful
        ];
    }
}
