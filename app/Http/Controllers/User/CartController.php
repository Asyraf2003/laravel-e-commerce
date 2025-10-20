<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        $this->authorize('viewAny', CartItem::class);
        $items = CartItem::query()
            ->inCart()
            ->where('user_id', Auth::id())
            ->with(['product:id,name,slug,stock,original_price,discount_percent', 'product.primaryImage'])
            ->orderByDesc('id')
            ->get();

        $summary = $this->makeSummary($items);
        return view('cart.index', compact('items', 'summary'));
    }
    
    /**
     * Menambahkan produk ke keranjang.
     */
    public function add(Request $request, Product $product)
    {
        $this->authorize('create', CartItem::class);
        $userId = Auth::id();
        $qty = max(1, (int) $request->integer('qty', 1));
        $this->assertProductBuyable($product);

        $existingItem = CartItem::query()->inCart()->where('user_id', $userId)->where('product_id', $product->id)->first();
        
        if ($existingItem) {
            $newQty = $existingItem->qty + $qty;
            $stock = (int) ($product->stock ?? 0);
            if ($stock > 0 && $newQty > $stock) {
                return back()->withErrors(['cart' => 'Kuantitas melebihi stok yang tersedia.']);
            }
            $existingItem->update(['qty' => $newQty, 'price_each' => (int) $product->final_price]);
        } else {
            $stock = (int) ($product->stock ?? 0);
            if ($stock > 0 && $qty > $stock) {
                return back()->withErrors(['cart' => 'Kuantitas melebihi stok yang tersedia.']);
            }
            CartItem::create([
                'user_id'   => $userId,
                'product_id'=> $product->id,
                'qty'       => $qty,
                'price_each'=> (int) $product->final_price,
                'product_name_snapshot' => $product->name,
                'sku_snapshot'          => $product->sku ?? null,
                'weight_snapshot'       => (int) ($product->weight ?? 0),
                'status'    => CartItem::STATUS_IN_CART,
            ]);
        }
        return back()->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Mengupdate kuantitas item di keranjang.
     */
    public function update(Request $request, CartItem $item)
    {
        $this->authorize('update', $item);
        $data = $request->validate(['qty' => ['required','integer','min:1']]);
        $item->load('product');
        $qty = (int) $data['qty'];
        $stock = (int) ($item->product->stock ?? 0);

        if ($stock > 0 && $qty > $stock) {
            return back()->withErrors(['qty' => 'Kuantitas melebihi stok untuk item ' . $item->product->name]);
        }
        
        $item->update(['qty' => $qty]);
        return back()->with('status', 'Kuantitas item berhasil diperbarui.');
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(CartItem $item)
    {
        $this->authorize('delete', $item);
        $item->delete();
        return back()->with('status', 'Item berhasil dihapus dari keranjang.');
    }

    // ===== Helpers =====
    private function makeSummary($items): array
    {
        return [
            'items_count'   => $items->sum('qty'),
            'subtotal'      => $items->sum('line_total'),
            'subtotal_text' => 'Rp' . number_format($items->sum('line_total'), 0, ',', '.'),
        ];
    }

    private function assertProductBuyable(Product $product): void
    {
        if (! $product->is_active || ($product->published_at && $product->published_at->isFuture())) {
            throw ValidationException::withMessages(['product' => 'Produk tidak tersedia.']);
        }
        if ((int) ($product->stock ?? 0) <= 0) {
            throw ValidationException::withMessages(['product' => 'Stok produk habis.']);
        }
    }
}

