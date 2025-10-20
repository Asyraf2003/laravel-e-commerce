<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class CheckoutAdminController extends Controller
{
    /**
     * Daftar orders (filter: q, transaction_status, status, from, to)
     */
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q'));
        $ts   = $request->query('transaction_status'); // pending/settlement/dll
        $stat = $request->query('status');             // draft/pending_payment/paid/cancelled/...
        $from = $request->query('from');               // YYYY-MM-DD
        $to   = $request->query('to');                 // YYYY-MM-DD

        $orders = Order::query()
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('order_no', 'like', "%{$q}%")
                      ->orWhere('recipient_name', 'like', "%{$q}%")
                      ->orWhere('recipient_phone', 'like', "%{$q}%");
                });
            })
            ->when($ts, fn($qr) => $qr->where('transaction_status', $ts))
            ->when($stat, fn($qr) => $qr->where('status', $stat))
            ->when($from, function ($qr) use ($from) {
                $qr->where(function ($w) use ($from) {
                    $w->whereDate('placed_at', '>=', $from)
                      ->orWhere(function ($x) use ($from) {
                          $x->whereNull('placed_at')->whereDate('created_at', '>=', $from);
                      });
                });
            })
            ->when($to, function ($qr) use ($to) {
                $qr->where(function ($w) use ($to) {
                    $w->whereDate('placed_at', '<=', $to)
                      ->orWhere(function ($x) use ($to) {
                          $x->whereNull('placed_at')->whereDate('created_at', '<=', $to);
                      });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.checkout.index', compact('orders'));
    }

    /**
     * Detail order
     */
    public function show(Order $order)
    {
        // load relasi jika perlu:
        // $order->load(['user','items.product']);
        return view('admin.checkout.detail', compact('order'));
    }

    /**
     * Hapus order (ikuti rule di OrderPolicy)
     */
    public function destroy(Order $order)
    {
        $no = $order->order_no;
        $order->delete();

        return redirect()
            ->route('admin.checkout.index')
            ->with('status', "Order {$no} telah dihapus.");
    }

    /**
     * Tandai PAID (mengikuti skema status di model kamu)
     *
     * - status bisnis -> Order::STATUS_PAID
     * - paid_at -> now() jika null
     * - transaction_status -> 'settlement' (selaras Midtrans)
     * - fraud_status -> 'accept' (opsional, tapi selaras)
     */
    public function markPaid(Order $order)
    {
        $update = [
            'status'             => Order::STATUS_PAID,
            'transaction_status' => 'settlement',
            'fraud_status'       => $order->fraud_status ?: 'accept',
        ];

        if (empty($order->paid_at)) {
            $update['paid_at'] = Carbon::now();
        }

        $order->fill($update)->save();

        return back()->with('status', "Order {$order->order_no} ditandai PAID.");
    }

    /**
     * Tandai UNPAID (kembali ke pending_payment)
     *
     * - status bisnis -> Order::STATUS_PENDING_PAYMENT
     * - paid_at -> null
     * - transaction_status -> 'pending' (agar konsisten)
     */
    public function markUnpaid(Order $order)
    {
        $order->fill([
            'status'             => Order::STATUS_PENDING_PAYMENT,
            'paid_at'            => null,
            'transaction_status' => 'pending',
        ])->save();

        return back()->with('status', "Order {$order->order_no} ditandai UNPAID.");
    }

    /**
     * Export CSV sesuai filter index()
     */
    public function export(Request $request)
    {
        $q    = trim((string) $request->query('q'));
        $ts   = $request->query('transaction_status');
        $stat = $request->query('status');
        $from = $request->query('from');
        $to   = $request->query('to');

        $rows = Order::query()
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('order_no', 'like', "%{$q}%")
                      ->orWhere('recipient_name', 'like', "%{$q}%")
                      ->orWhere('recipient_phone', 'like', "%{$q}%");
                });
            })
            ->when($ts, fn($qr) => $qr->where('transaction_status', $ts))
            ->when($stat, fn($qr) => $qr->where('status', $stat))
            ->when($from, function ($qr) use ($from) {
                $qr->where(function ($w) use ($from) {
                    $w->whereDate('placed_at', '>=', $from)
                      ->orWhere(function ($x) use ($from) {
                          $x->whereNull('placed_at')->whereDate('created_at', '>=', $from);
                      });
                });
            })
            ->when($to, function ($qr) use ($to) {
                $qr->where(function ($w) use ($to) {
                    $w->whereDate('placed_at', '<=', $to)
                      ->orWhere(function ($x) use ($to) {
                          $x->whereNull('placed_at')->whereDate('created_at', '<=', $to);
                      });
                });
            })
            ->orderByDesc('created_at')
            ->get([
                'id','order_no','user_id',
                'recipient_name','recipient_phone',
                'province_name','city_name','subdistrict_name','destination_level',
                'address','postal_code',
                'courier','service','shipping_cost','weight_total','discount_total','tax_total',
                'subtotal','total','currency',
                'payment_gateway','transaction_status','fraud_status',
                'gross_amount','midtrans_order_id','payment_token','payment_redirect_url',
                'status','placed_at','paid_at','created_at','updated_at',
            ]);

        $filename = 'orders_export_'.now()->format('Ymd_His').'.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'id','order_no','user_id',
                'recipient_name','recipient_phone',
                'province_name','city_name','subdistrict_name','destination_level',
                'address','postal_code',
                'courier','service','shipping_cost','weight_total','discount_total','tax_total',
                'subtotal','total','currency',
                'payment_gateway','transaction_status','fraud_status',
                'gross_amount','midtrans_order_id','payment_token','payment_redirect_url',
                'status','placed_at','paid_at','created_at','updated_at',
            ]);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id, $r->order_no, $r->user_id,
                    $r->recipient_name, $r->recipient_phone,
                    $r->province_name, $r->city_name, $r->subdistrict_name, $r->destination_level,
                    $r->address, $r->postal_code,
                    $r->courier, $r->service, $r->shipping_cost, $r->weight_total, $r->discount_total, $r->tax_total,
                    $r->subtotal, $r->total, $r->currency,
                    $r->payment_gateway, $r->transaction_status, $r->fraud_status,
                    $r->gross_amount, $r->midtrans_order_id, $r->payment_token, $r->payment_redirect_url,
                    $r->status,
                    optional($r->placed_at)->toDateTimeString(),
                    optional($r->paid_at)->toDateTimeString(),
                    optional($r->created_at)->toDateTimeString(),
                    optional($r->updated_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
    }
}
