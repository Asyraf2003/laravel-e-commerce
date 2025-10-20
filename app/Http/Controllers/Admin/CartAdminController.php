<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CartItem::class);

        $q         = (string) $request->string('q');
        $status    = (string) $request->string('status');
        $userId    = $request->integer('user_id') ?: null;
        $productId = $request->integer('product_id') ?: null;

        $items = CartItem::query()
            ->with(['user:id,name,email', 'product:id,name,stock'])
            ->when($q, fn($qr) => $qr->where('product_name_snapshot', 'like', "%{$q}%"))
            ->when($status !== '', fn($qr) => $qr->where('status', $status))
            ->when($userId, fn($qr) => $qr->where('user_id', $userId))
            ->when($productId, fn($qr) => $qr->where('product_id', $productId))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.carts.index', compact('items','q','status','userId','productId'));
    }

    public function user(User $user)
    {
        $this->authorize('viewAny', CartItem::class);

        $items = CartItem::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();

        $summary = $this->summary($items);

        return view('admin.carts.user', compact('user','items','summary'));
    }

    public function updateQty(Request $request, CartItem $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate(['qty' => ['required','integer','min:1']]);

        $item->load('product:id,stock,original_price,discount_percent');

        $qty   = (int) $validated['qty'];
        $stock = (int) ($item->product?->stock ?? 0);
        if ($stock > 0 && $qty > $stock) {
            return back()->withErrors(['qty' => 'Qty melebihi stok.']);
        }

        $item->qty = $qty;
        if ($item->product) $item->price_each = (int) $item->product->final_price;
        $item->save();

        return back()->with('status', 'Qty updated');
    }

    public function changeStatus(Request $request, CartItem $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                CartItem::STATUS_IN_CART,
                CartItem::STATUS_SAVED,
                CartItem::STATUS_REMOVED,
                CartItem::STATUS_CHECKEDOUT,
            ])],
        ]);

        $item->status = $validated['status'];
        $item->save();

        return back()->with('status', 'Status updated');
    }

    public function destroy(CartItem $item)
    {
        $this->authorize('delete', $item);

        $item->status = CartItem::STATUS_REMOVED;
        $item->save();

        return back()->with('status', 'Item removed');
    }

    public function clearUser(User $user)
    {
        $this->authorize('viewAny', CartItem::class);

        CartItem::query()
            ->inCart()
            ->where('user_id', $user->id)
            ->update(['status' => CartItem::STATUS_REMOVED]);

        return back()->with('status', 'Keranjang user dikosongkan');
    }

    private function summary($items): array
    {
        $qty=0; $subtotal=0; $weight=0;
        foreach ($items as $it) {
            $qty += (int)$it->qty;
            $subtotal += (int)$it->line_total;
            $weight += (int)$it->weight_snapshot * (int)$it->qty;
        }
        return [
            'items_count'   => $qty,
            'subtotal'      => $subtotal,
            'weight'        => $weight,
            'subtotal_text' => 'Rp' . number_format($subtotal, 0, ',', '.'),
        ];
    }
}
