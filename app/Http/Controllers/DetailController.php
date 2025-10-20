<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;

class DetailController extends Controller
{
    public function detail($id)
    {
        // Product + relasi untuk halaman detail
        $product = Product::with(['primaryImage', 'images', 'category'])->findOrFail($id);

        // ===== Reviews (hanya approved) =====
        $approvedBase = ProductReview::query()
            ->approved()
            ->where('product_id', $product->id);

        // statistik
        $avgRating    = round((float) $approvedBase->avg('rating') ?: 0, 1);
        $reviewsCount = (int) $approvedBase->count();

        // daftar review (user sudah di-eager load), urut yang paling baru di-approve
        $reviews = $approvedBase
            ->with(['user:id,name'])
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // ===== Related products =====
        $relatedProducts = Product::query()
            ->with(['primaryImage', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest('id')
            ->take(8)
            ->get();

        return view('shop.detail', compact(
            'product', 'avgRating', 'reviewsCount', 'reviews', 'relatedProducts'
        ));
    }
}
