<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Subquery ambil product_id yang di-wishlist user ini
        $wishlistSub = Wishlist::query()
            ->select('product_id')
            ->where('user_id', $userId);

        // Produk di wishlist + agregat rating approved
        $products = Product::query()
            ->with(['primaryImage', 'category'])
            ->withAvg(['reviews as approved_reviews_avg' => fn ($r) => $r->approved()], 'rating')
            ->withCount(['reviews as approved_reviews_count' => fn ($r) => $r->approved()])
            ->whereIn('id', $wishlistSub)
            ->withExists([
                'wishlists as is_wishlisted' => fn ($q) => $q->where('user_id', $userId),
            ])
            ->orderByDesc(
                Wishlist::query()
                    ->select('created_at')
                    ->whereColumn('product_id', 'products.id')
                    ->where('user_id', $userId)
                    ->latest('created_at')
                    ->limit(1)
            )
            ->paginate(12); // jangan withQueryString() biar ?page lama nggak kebawa

        // Jika user "nyasar" ke page kosong (mis. ?page=99), balikin ke page 1
        if ($products->isEmpty() && $products->currentPage() > 1) {
            return redirect()->route('user.wishlist.index');
        }

        // JSON (kalau dibutuhkan di FE)
        if ($request->wantsJson()) {
            return response()->json([
                'items' => $products->getCollection()->map(fn ($p) => [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'image'       => $p->primaryImage->image_path ?? null,
                    'category'    => $p->category->name ?? null,
                    'price'       => (int) ($p->final_price ?? 0),
                    'rating_avg'  => (float) ($p->approved_reviews_avg ?? 0),
                    'rating_count'=> (int)   ($p->approved_reviews_count ?? 0),
                ]),
                'pagination' => [
                    'total'        => $products->total(),
                    'per_page'     => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page'    => $products->lastPage(),
                ],
            ]);
        }

        return view('shop.wishlist', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json(['ok'=>false,'message'=>'Unauthenticated'], 401);
        }

        $userId = Auth::id();

        $existing = Wishlist::withTrashed()
            ->owned($userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                return $this->jsonOk(['status' => 'added']);
            }
            $existing->delete();
            return $this->jsonOk(['status' => 'removed']);
        }

        Wishlist::create([
            'user_id'    => $userId,
            'product_id' => $product->id,
            'session_id' => null,
        ]);

        return $this->jsonOk(['status' => 'added']);
    }

    public function destroy(Request $request, Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === Auth::id(), 403);

        $wishlist->delete();

        if ($request->wantsJson()) {
            return $this->jsonOk(['status' => 'removed']);
        }

        return back()->with('status', 'Removed from wishlist');
    }

    public function count()
    {
        return response()->json([
            'total' => Wishlist::owned(Auth::id())->count(),
        ]);
    }

    private function jsonOk(array $data = [], int $code = 200)
    {
        return response()->json($data + ['ok' => true], $code);
    }
}
