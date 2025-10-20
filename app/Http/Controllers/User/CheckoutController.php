<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\RajaOngkirService;
use App\Services\MidtransHttp; // Pastikan service Midtrans Anda di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private RajaOngkirService $ongkir)
    {
    }

    public function show(Request $request)
    {
        $userId = Auth::id();
        $items = CartItem::query()->inCart()->where('user_id', $userId)->with(['product:id,name', 'product.primaryImage'])->get();

        if ($items->isEmpty()) {
            return redirect()->route('user.cart.index')->with('info', 'Keranjang Anda kosong.');
        }

        $subtotal = $items->sum('line_total');
        $weightTotal = $items->sum(fn($item) => (int)($item->weight_snapshot ?? 0) * $item->qty);
        $couriers = config('services.rajaongkir.couriers', ['jne','tiki','pos']);

        return view('checkout.show', compact('items', 'subtotal', 'weightTotal', 'couriers'));
    }

    public function store(Request $request)
    {
        $courierList = implode(',', config('services.rajaongkir.couriers'));
        $validated = $request->validate([
            'recipient_name'    => 'required|string|max:100',
            'recipient_phone'   => 'required|string|max:20',
            'address'           => 'required|string|max:255',
            'postal_code'       => 'nullable|string|max:10',
            'destination_id'    => 'required|integer',
            'province_name'     => 'required|string',
            'city_name'         => 'required|string',
            'subdistrict_name'  => 'required|string',
            'courier'           => "required|string|in:$courierList",
            'service'           => 'required|string|max:50',
            'shipping_cost'     => 'required|integer|min:0',
        ]);
        
        $userId = Auth::id();
        $cartItems = CartItem::inCart()->where('user_id', $userId)->with('product')->get();
        if($cartItems->isEmpty()){
             return redirect()->route('user.cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $subtotal = $cartItems->sum('line_total');
        $weightTotal = $cartItems->sum(fn($item) => (int)($item->weight_snapshot ?? 0) * $item->qty);

        $serverCosts = collect($this->ongkir->cost((int) config('services.rajaongkir.origin'), (int) $validated['destination_id'], $weightTotal, $validated['courier'], 'lowest'));
        $found = $serverCosts->first(fn($c) => ($c['service'] ?? '') === $validated['service']);
        abort_unless($found && ((int) ($found['value'] ?? -1) === (int) $validated['shipping_cost']), 422, 'Tarif ongkir tidak valid.');

        DB::beginTransaction();
        try {
            $total = $subtotal + $validated['shipping_cost'];
            $order = Order::create([
                'user_id'           => Auth::id(),'order_no'          => 'ORD-' . strtoupper(Str::random(10)),
                'status'            => Order::STATUS_DRAFT, 'subtotal' => $subtotal, 'weight_total'      => $weightTotal,
                'placed_at'         => now(), 'recipient_name'    => $validated['recipient_name'], 'recipient_phone'   => $validated['recipient_phone'],
                'address'           => $validated['address'], 'postal_code'       => $validated['postal_code'], 'province_name'     => $validated['province_name'],
                'city_name'         => $validated['city_name'], 'subdistrict_name'  => $validated['subdistrict_name'], 'courier' => $validated['courier'],
                'service'           => $validated['service'], 'shipping_cost'     => $validated['shipping_cost'], 'total' => $total,
                'payment_gateway'   => 'midtrans',
            ]);
            
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'              => $order->id, 'product_id'            => $cartItem->product_id,
                    'product_name_snapshot' => $cartItem->product_name_snapshot, 'sku_snapshot' => $cartItem->sku_snapshot,
                    'weight_snapshot'       => $cartItem->weight_snapshot, 'qty' => $cartItem->qty, 'price_each' => $cartItem->price_each,
                ]);
            }
            CartItem::whereIn('id', $cartItems->pluck('id'))->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order creation database transaction failed', ['error' => $e->getMessage()]);
            return redirect()->route('user.cart.index')->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
        }
        
        try {
            $midOrderId = 'ORD-' . $order->id . '-' . Str::upper(Str::random(5));
            $order->load('items');
            $itemDetails = $order->items->map(function ($item) {
                return [
                    'id'       => (string) $item->id, 'price'    => (int) $item->price_each,
                    'quantity' => (int) $item->qty, 'name'     => substr($item->product_name_snapshot, 0, 50),
                ];
            })->push(['id' => 'SHIPPING', 'price' => (int) $order->shipping_cost, 'quantity' => 1, 'name' => 'Ongkos Kirim'])->values()->all();

            if ((int) $order->total !== (int) collect($itemDetails)->sum(fn($item) => $item['price'] * $item['quantity'])) {
                throw new \Exception('Perhitungan total tidak cocok sebelum mengirim ke Midtrans.');
            }
            
            // **PERBAIKAN KUNCI:** Membersihkan nomor telepon dari karakter non-numerik.
            $sanitizedPhone = preg_replace('/[^0-9]/', '', $order->recipient_phone);

            $payload = [
                'transaction_details' => ['order_id' => $midOrderId, 'gross_amount' => (int) $order->total],
                'customer_details'    => ['first_name' => $order->recipient_name, 'email' => $request->user()->email, 'phone' => $sanitizedPhone],
                'item_details'        => $itemDetails,
                'callbacks'           => ['finish' => route('user.orders.finish', $order)],
            ];

            $snap = app(MidtransHttp::class)->createSnap($payload);

            $token = $snap['token'] ?? null;
            $redirectUrl = $snap['redirect_url'] ?? null;
            if (!$token || !$redirectUrl) {
                throw new \Exception('Midtrans Snap response missing token or redirect_url.');
            }

            $order->update([
                'midtrans_order_id'    => $midOrderId, 'status'               => Order::STATUS_PENDING_PAYMENT,
                'payment_token'        => $token, 'payment_redirect_url' => $redirectUrl,
                'midtrans_payload'     => $snap['raw'] ?? null, 'gross_amount'         => (int) $order->total,
            ]);

            return redirect()->away($redirectUrl);

        } catch (\Throwable $e) {
            Log::error('Midtrans Snap creation failed', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return redirect()->route('user.history.show', $order)->with('error', 'Pesanan Anda telah dibuat, namun gagal membuat sesi pembayaran. Silakan coba bayar dari halaman pesanan Anda.');
        }
    }

    public function searchDestination(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        return response()->json($this->ongkir->searchDestination($request->q, 15));
    }

    public function getShippingCosts(Request $request)
    {
        $data = $request->validate([
            'destination_id' => 'required|integer', 'courier' => 'required|string', 'weight' => 'required|integer|min:1',
        ]);
        $originId = (int) config('services.rajaongkir.origin');
        $costs = $this->ongkir->cost($originId, $data['destination_id'], $data['weight'], $data['courier'], 'lowest');
        return response()->json(['options' => $costs]);
    }
}

