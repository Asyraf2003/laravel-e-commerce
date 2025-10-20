<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $q            = trim($request->get('q', ''));
        $categorySlug = $request->get('category');

        $activeCategory = $categorySlug
            ? Category::query()->where('slug', $categorySlug)->first()
            : null;

        $productsQuery = Product::query()
            ->with(['primaryImage', 'category'])
            // agregat per-produk hanya untuk review approved
            ->withAvg(['reviews as approved_reviews_avg' => fn($r) => $r->approved()], 'rating')
            ->withCount(['reviews as approved_reviews_count' => fn($r) => $r->approved()])
            ->when($activeCategory, fn ($q2) => $q2->where('category_id', $activeCategory->id))
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->latest('id');

        $products = $productsQuery->paginate(12)->withQueryString();

        // Kalau user nyasar ke page kosong (mis. ?page=99), balikin ke page 1
        if ($products->isEmpty() && $products->currentPage() > 1) {
            return redirect()->route('shop.index', array_filter([
                'q'        => $q ?: null,
                'category' => $categorySlug ?: null,
            ]));
        }

        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            $gridHtml       = view('partials._products-grid', ['products' => $products])->render();
            $paginationHtml = $products->onEachSide(1)->links()->toHtml();

            return response()->json([
                'grid'       => $gridHtml,
                'pagination' => $paginationHtml,
                'meta' => [
                    'total'      => $products->total(),
                    'per_page'   => $products->perPage(),
                    'page'       => $products->currentPage(),
                    'last_page'  => $products->lastPage(),
                ],
            ]);
        }

        // HAPUS perhitungan global @approvedBase, avgRating, reviewsCount — nggak relevan di listing
        return view('shop.index', compact('products', 'categories', 'activeCategory'));
    }
}
