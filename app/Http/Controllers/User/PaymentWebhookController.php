<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming Midtrans notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1. Validasi Keaslian Notifikasi (Signature Key)
        if (!$this->isSignatureKeyValid($payload)) {
            Log::warning('Midtrans webhook: Invalid signature.', ['payload' => $payload]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Temukan Order di Database
        $order = $this->getOrderFromPayload($payload);
        if (!$order) {
            Log::warning('Midtrans webhook: Order not found.', ['order_id' => $payload['order_id'] ?? null]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 3. Update Status Order
        // Gunakan method 'applyMidtransStatus' yang sudah ada di model Order Anda.
        // Ini adalah cara yang bersih dan terpusat untuk mengelola logika status.
        try {
            DB::transaction(function () use ($order, $payload) {
                // Panggil helper di model untuk menentukan status baru (paid, cancelled, dll.)
                $order->applyMidtransStatus($payload['transaction_status'], $payload['fraud_status'] ?? null);

                // Simpan detail tambahan dari Midtrans
                $order->transaction_id = $payload['transaction_id'] ?? $order->transaction_id;
                $order->payment_type = $payload['payment_type'] ?? $order->payment_type;
                $order->settlement_time = $payload['settlement_time'] ?? $order->settlement_time;
                $order->status_code = $payload['status_code'] ?? $order->status_code;
                $order->status_message = $payload['status_message'] ?? $order->status_message;
                $order->va_numbers = $payload['va_numbers'] ?? $order->va_numbers;
                $order->midtrans_payload = $payload; // Simpan seluruh payload untuk audit

                $order->save();
            });
        } catch (\Exception $e) {
            Log::error('Midtrans webhook: Failed to update order status.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
        
        Log::info('Midtrans webhook: Successfully processed.', ['order_id' => $order->id, 'new_status' => $order->status]);
        return response()->json(['message' => 'OK']);
    }

    /**
     * Validate the signature key from Midtrans payload.
     */
    private function isSignatureKeyValid(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $serverKey = config('services.midtrans.server_key');

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey || !$serverKey) {
            return false;
        }

        $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($localSignature, $signatureKey);
    }

    /**
     * Find the local order from the Midtrans order_id format.
     */
    private function getOrderFromPayload(array $payload): ?Order
    {
        $midtransOrderId = $payload['order_id'] ?? null;
        if (!$midtransOrderId) {
            return null;
        }
        
        // Cari Order berdasarkan midtrans_order_id yang Anda simpan saat membuat transaksi Snap
        return Order::where('midtrans_order_id', $midtransOrderId)->first();
    }
}