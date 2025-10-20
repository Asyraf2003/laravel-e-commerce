<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected string $base;
    protected string $key;

    public function __construct()
    {
        $this->base = rtrim(config('services.rajaongkir.base'), '/');
        $this->key  = (string) config('services.rajaongkir.key');
    }

    protected function client()
    {
        return Http::withHeaders(['key' => $this->key])
            ->acceptJson()
            ->timeout(20)
            ->retry(2, 250)
            ->throw();
    }

    /** Search destination (city/district/subdistrict/zip) */
    public function searchDestination(string $query, int $limit = 10, int $offset = 0): array
    {
        $res = $this->client()->get($this->base . '/destination/domestic-destination', [
            'search' => $query,
            'limit'  => $limit,
            'offset' => $offset,
        ]);
        $json = $res->json();
        return $json['data'] ?? [];
    }

    /** Hitung ongkir Komerce */
    public function cost(int $originId, int $destinationId, int $weightGram, string $courier, string $pricePref = 'lowest'): array
    {
        $res = $this->client()->asForm()->post($this->base . '/calculate/domestic-cost', [
            'origin'      => $originId,
            'destination' => $destinationId,
            'weight'      => max(1, $weightGram),
            'courier'     => $courier,
            'price'       => $pricePref, // lowest | highest
        ]);

        $json = $res->json();
        $list = [];
        foreach (($json['data'] ?? []) as $row) {
            $list[] = [
                'courier'     => $row['code'] ?? $courier,
                'service'     => $row['service'] ?? '',
                'description' => $row['description'] ?? '',
                'value'       => (int) ($row['cost'] ?? 0),
                'etd'         => $row['etd'] ?? null,
            ];
        }
        return $list;
    }
}
