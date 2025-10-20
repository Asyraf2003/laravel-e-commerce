<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductReview;
use App\Policies\Concerns\AdminBypass;

class ProductReviewPolicy
{
    use AdminBypass;

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ProductReview $review): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function createForProduct(User $user, Product $product): bool
    {
        if (!$user->is_active) {
            return false;
        }

        return !$user->productReviews()
            ->where('product_id', $product->id)
            ->exists();
    }

    public function update(User $user, ProductReview $review): bool
    {
        return $review->user_id === $user->id;
    }

    public function delete(User $user, ProductReview $review): bool
    {
        return $review->user_id === $user->id;
    }

    public function approve(User $user, ProductReview $review): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, ProductReview $review): bool
    {
        return $user->isAdmin();
    }
}
