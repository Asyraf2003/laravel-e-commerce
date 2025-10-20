<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReviewVote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan produk dan user sudah ada
        if (Product::count() === 0 || User::count() === 0) {
            $this->call([ProductSeeder::class, UserSeeder::class]);
        }
        
        // Nonaktifkan Foreign Key Checks untuk Truncate
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
        ProductReview::truncate();
        DB::table('review_votes')->truncate(); // Nama tabel ReviewVote adalah review_votes
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

        $productIds = Product::pluck('id');
        $userIds = User::pluck('id');
        $reviews = collect();
        $totalReviewCount = 0;

        // 1. BUAT REVIEWS: Distribusikan 300-400 reviews
        foreach ($productIds as $productId) {
            
            $reviewCount = rand(4, 6);
            $reviewedUserIds = []; // Melacak user yang sudah mereview produk ini
            
            // Tentukan jumlah maksimal ulasan unik yang bisa dibuat (min antara count random dan total user)
            $maxReviewsPossible = min($reviewCount, $userIds->count());

            for ($i = 0; $i < $maxReviewsPossible; $i++) {
                
                // Ambil daftar user yang BELUM mereview produk ini
                $availableUserIds = $userIds->diff($reviewedUserIds);
                
                // Jika tidak ada user unik yang tersisa, hentikan looping untuk produk ini
                if ($availableUserIds->isEmpty()) {
                    break;
                }

                // Pilih ID user unik secara acak
                $userId = $availableUserIds->random();
                $reviewedUserIds[] = $userId; // Tandai user ini sudah digunakan

                // Buat review dengan ID unik
                $review = ProductReview::factory()->make([
                    'product_id' => $productId,
                    'user_id' => $userId,
                ]);
                $review->save();

                $reviews->push($review);
                $totalReviewCount++;
            }
        }

        $this->command->info('Total ' . $totalReviewCount . ' reviews created.');


        // 2. BUAT VOTES: Tambahkan votes ke sekitar 40% dari total reviews
        // (Logika voting tetap sama)
        $reviewIds = ProductReview::pluck('id');
        
        $voteTarget = 120;
        
        // Gunakan $reviews yang baru dibuat (sudah unik)
        $reviewsToVote = $reviews->random(min($voteTarget, $reviews->count())); 

        foreach ($reviewsToVote as $review) {
            // Setiap review yang terpilih mendapatkan 1 vote
            $vote = ReviewVote::factory()->make([
                'review_id' => $review->id,
                // Pastikan user yang vote berbeda dengan user yang membuat review
                'user_id' => $userIds->except($review->user_id)->random() ?? $userIds->random(), 
            ]);
            $vote->save();
        }

        $this->command->info('Total ' . ReviewVote::count() . ' votes created.');
    }
}
