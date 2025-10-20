<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransHttp
{
    protected string $snapBase;
    protected string $coreBase;
    protected string $serverKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->isProduction = filter_var(config('services.midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);
        $this->snapBase  = $this->isProduction ? 'https://app.midtrans.com/snap/v1' : 'https://app.sandbox.midtrans.com/snap/v1';
        $this->coreBase  = $this->isProduction ? 'https://api.midtrans.com/v2'     : 'https://api.sandbox.midtrans.com/v2';
        $this->serverKey = (string) config('services.midtrans.server_key');
    }

    protected function authHeader(): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    public function createSnap(array $payload): array
    {
        $res = Http::withHeaders($this->authHeader())
            ->post($this->snapBase . '/transactions', $payload);

        if (!$res->successful()) {
            Log::error('Midtrans Snap error', ['status' => $res->status(), 'body' => $res->json()]);
            $res->throw();
        }

        $json = $res->json();
        return [
            'token'        => $json['token'] ?? null,
            'redirect_url' => $json['redirect_url'] ?? null,
            'raw'          => $json,
        ];
    }

    public function status(string $orderId): array
    {
        $res = Http::withHeaders($this->authHeader())
            ->get($this->coreBase . '/' . $orderId . '/status');

        if (!$res->successful()) {
            Log::error('Midtrans Status error', ['status' => $res->status(), 'body' => $res->json()]);
            $res->throw();
        }

        return $res->json();
    }
}
