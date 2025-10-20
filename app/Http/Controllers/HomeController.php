<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $agg = fn ($r) => $r->approved();

        $relations = ['primaryImage', 'category', 'reviews'];

        $product = Product::with('category', 'images', 'reviews')->find(3);
        
        $bannerProduct = Product::active()
            ->where('is_new', true)
            ->with($relations)
            ->inRandomOrder()
            ->first();

        $leftBannerProduct = \App\Models\Product::active()
            ->with($relations)->inRandomOrder()->first();

        $rightBannerProduct = \App\Models\Product::active()
            ->when($leftBannerProduct, fn($q) => $q->where('id', '!=', $leftBannerProduct->id))
            ->with($relations)->inRandomOrder()->first();
            
        $allProducts       = Product::with($relations)->latest()->take(8)->get();
        $allProducts = Product::with($relations)
            ->withAvg(['reviews as approved_reviews_avg' => $agg], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $agg])
            ->latest()->take(8)->get();
        $newProducts = Product::where('is_new', true)->with($relations)
            ->withAvg(['reviews as approved_reviews_avg' => $agg], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $agg])
            ->latest()->take(8)->get();
        $featuredProducts  = Product::where('is_featured', true)->with($relations)
            ->withAvg(['reviews as approved_reviews_avg' => $agg], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $agg])
            ->latest()->take(8)->get();
        $bestSellerProducts= Product::where('is_best_seller', true)->with($relations)
            ->withAvg(['reviews as approved_reviews_avg' => $agg], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $agg])
            ->latest()->take(8)->get();
        $hotProducts       = Product::where('is_hot', true)->with($relations)
            ->withAvg(['reviews as approved_reviews_avg' => $agg], 'rating')
            ->withCount(['reviews as approved_reviews_count' => $agg])
            ->latest()->take(8)->get();

        return view('welcome', compact(
            'product',
            'bannerProduct',
            'leftBannerProduct',
            'rightBannerProduct',
            'allProducts',
            'newProducts',
            'featuredProducts',
            'bestSellerProducts',
            'hotProducts'
        ));
    }
}
