<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransHttp; // Pastikan Anda memiliki service ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderPaymentController extends Controller
{
    /**
     * Handle the redirect back from Midtrans after payment attempt.
     */
    public function finish(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        try {
            // **DIPERBAIKI:** Periksa apakah ID Midtrans ada sebelum melanjutkan.
            if (empty($order->midtrans_order_id)) {
                throw new \Exception('Midtrans Order ID not found for this order.');
            }

            $midtrans = app(MidtransHttp::class);
            $statusPayload = $midtrans->status($order->midtrans_order_id);
    
            if (!isset($statusPayload['transaction_status'])) {
                throw new \Exception('Midtrans status response did not contain a transaction_status key.');
            }

            DB::transaction(function () use ($order, $statusPayload) {
                $order->applyMidtransStatus($statusPayload['transaction_status'], $statusPayload['fraud_status'] ?? null);
                $order->midtrans_payload = $statusPayload;
                $order->save();
            });

            $statusText = ucfirst(str_replace('_', ' ', $order->status));
            return redirect()->route('user.history.show', $order)->with('status', "Status pembayaran untuk pesanan #{$order->order_no} adalah: {$statusText}");

        } catch (\Exception $e) {
            Log::error('Failed to get Midtrans status on finish callback.', [
                'order_id' => $order->id, 'error' => $e->getMessage(), 'midtrans_payload' => $statusPayload ?? 'Payload not available'
            ]);
            return redirect()->route('user.history.show', $order)->with('error', 'Gagal memverifikasi status pembayaran. Silakan coba lagi nanti.');
        }
    }

    /**
     * Manually refresh the payment status of an order.
     */
    public function refresh(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        try {
            // **DIPERBAIKI:** Periksa apakah ID Midtrans ada.
            if (empty($order->midtrans_order_id)) {
                throw new \Exception('Midtrans Order ID not found for this order.');
            }

            $midtrans = app(MidtransHttp::class);
            $statusPayload = $midtrans->status($order->midtrans_order_id);

            if (!isset($statusPayload['transaction_status'])) {
                throw new \Exception('Midtrans status response did not contain a transaction_status key.');
            }

            DB::transaction(function () use ($order, $statusPayload) {
                $order->applyMidtransStatus($statusPayload['transaction_status'], $statusPayload['fraud_status'] ?? null);
                $order->midtrans_payload = $statusPayload;
                $order->save();
            });
            
            $statusText = ucfirst(str_replace('_', ' ', $order->status));
            return back()->with('status', "Status pembayaran berhasil diperbarui: {$statusText}");

        } catch (\Exception $e) {
            Log::error('Failed to refresh Midtrans status.', [
                'order_id' => $order->id, 'error' => $e->getMessage(), 'midtrans_payload' => $statusPayload ?? 'Payload not available'
            ]);
            return back()->with('error', 'Gagal memperbarui status pembayaran saat ini.');
        }
    }
}

