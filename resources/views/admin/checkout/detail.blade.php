<x-mazer-layout>
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Order Detail</h3>
            <p class="text-muted mb-0">Ringkasan lengkap transaksi & payload Midtrans.</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">{{ $order->order_no }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.checkout.index') }}" class="btn btn-light">Kembali</a>
                            <button class="btn btn-outline-secondary" onclick="copyText('{{ $order->order_no }}', this)">Copy Order No</button>
                            @if (!empty($order->payment_redirect_url))
                                <a href="{{ $order->payment_redirect_url }}" target="_blank" class="btn btn-primary">Buka Payment URL</a>
                                <button class="btn btn-outline-secondary" onclick="copyText('{{ $order->payment_redirect_url }}', this)">Copy Payment URL</button>
                            @endif
                            <form action="{{ route('admin.checkout.destroy', $order->id) }}" method="POST"
                                onsubmit="return confirm('Hapus order {{ $order->order_no }}?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Status badges --}}
                        <div class="mb-3">
                            @php
                                $txn = $order->transaction_status ?? '-';
                                $map = [
                                'settlement'=>'success','capture'=>'success','authorize'=>'primary',
                                'pending'=>'warning','challenge'=>'warning',
                                'deny'=>'danger','cancel'=>'secondary','expire'=>'secondary','failure'=>'danger'
                                ];
                                $b2 = ['paid'=>'success','unpaid'=>'secondary','refunded'=>'info'][$order->status ?? ''] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $map[$txn] ?? 'secondary' }} me-1">{{ $txn }}</span>
                            <span class="badge bg-{{ $b2 }}">{{ $order->status ?? '-' }}</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-uppercase text-muted">Penerima</h6>
                                                <dl class="row">
                                                    <dt class="col-5">Nama</dt><dd class="col-7">{{ $order->recipient_name ?? '-' }}</dd>
                                                    <dt class="col-5">Telepon</dt><dd class="col-7">{{ $order->recipient_phone ?? '-' }}</dd>
                                                    <dt class="col-5">Provinsi</dt><dd class="col-7">{{ $order->province_name ?? '-' }}</dd>
                                                    <dt class="col-5">Kota/Kab.</dt><dd class="col-7">{{ $order->city_name ?? '-' }}</dd>
                                                    <dt class="col-5">Kecamatan</dt><dd class="col-7">{{ $order->subdistrict_name ?? '-' }}</dd>
                                                    <dt class="col-5">Level</dt><dd class="col-7">{{ $order->destination_level ?? '-' }}</dd>
                                                    <dt class="col-5">Kode Pos</dt><dd class="col-7">{{ $order->postal_code ?? '-' }}</dd>
                                                    <dt class="col-5">Alamat</dt><dd class="col-7">{{ $order->address ?? '-' }}</dd>
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-uppercase text-muted">Pengiriman</h6>
                                                <dl class="row">
                                                    <dt class="col-5">Kurir</dt><dd class="col-7 text-uppercase">{{ $order->courier ?? '-' }}</dd>
                                                    <dt class="col-5">Service</dt><dd class="col-7">{{ $order->service ?? '-' }}</dd>
                                                    <dt class="col-5">Berat Total</dt><dd class="col-7">{{ (int)($order->weight_total ?? 0) }} g</dd>
                                                    <dt class="col-5">Ongkir</dt><dd class="col-7">{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->shipping_cost ?? 0), 0, ',', '.') }}</dd>
                                                </dl>

                                                <h6 class="text-uppercase text-muted">Pembayaran</h6>
                                                <dl class="row">
                                                    <dt class="col-5">Gateway</dt><dd class="col-7 text-uppercase">{{ $order->payment_gateway ?? '-' }}</dd>
                                                    <dt class="col-5">Midtrans Order</dt><dd class="col-7">{{ $order->midtrans_order_id ?? '-' }}</dd>
                                                    <dt class="col-5">Payment Token</dt><dd class="col-7"><code class="small">{{ $order->payment_token ?? '-' }}</code></dd>
                                                    <dt class="col-5">Gross Amount</dt><dd class="col-7">{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->gross_amount ?? 0), 0, ',', '.') }}</dd>
                                                    <dt class="col-5">Fraud</dt><dd class="col-7">{{ $order->fraud_status ?? '-' }}</dd>
                                                </dl>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-uppercase text-muted">Ringkasan Biaya</h6>
                                                <dl class="row">
                                                    <dt class="col-5">Subtotal</dt><dd class="col-7">{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->subtotal ?? 0), 0, ',', '.') }}</dd>
                                                    <dt class="col-5">Diskon</dt><dd class="col-7">- {{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->discount_total ?? 0), 0, ',', '.') }}</dd>
                                                    <dt class="col-5">Pajak</dt><dd class="col-7">{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->tax_total ?? 0), 0, ',', '.') }}</dd>
                                                    <dt class="col-5">Ongkir</dt><dd class="col-7">{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->shipping_cost ?? 0), 0, ',', '.') }}</dd>
                                                    <dt class="col-5">Total</dt><dd class="col-7 fw-semibold">{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->total ?? 0), 0, ',', '.') }}</dd>
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-uppercase text-muted">Waktu</h6>
                                                <dl class="row">
                                                    <dt class="col-5">Placed At</dt><dd class="col-7">{{ optional($order->placed_at)->format('Y-m-d H:i:s') ?? '-' }}</dd>
                                                    <dt class="col-5">Paid At</dt><dd class="col-7">{{ optional($order->paid_at)->format('Y-m-d H:i:s') ?? '-' }}</dd>
                                                    <dt class="col-5">Created</dt><dd class="col-7">{{ optional($order->created_at)->format('Y-m-d H:i:s') ?? '-' }}</dd>
                                                    <dt class="col-5">Updated</dt><dd class="col-7">{{ optional($order->updated_at)->format('Y-m-d H:i:s') ?? '-' }}</dd>
                                                </dl>
                                            </div>
                                        </div>

                                        <hr>

                                        <h6 class="text-uppercase text-muted">Midtrans Payload</h6>
                                        @php
                                            $pretty = null;
                                            try {
                                                $pretty = json_encode(is_string($order->midtrans_payload)
                                                    ? json_decode($order->midtrans_payload, true)
                                                    : $order->midtrans_payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                                            } catch(\Throwable $e) {}
                                        @endphp
                                        <div class="position-relative">
                                            <button class="btn btn-sm btn-outline-secondary position-absolute end-0 top-0 mt-1 me-1"
                                                    onclick="copyText(document.getElementById('payload').innerText, this)">Copy</button>
                                            <pre id="payload" class="bg-light p-3 rounded small mb-0" style="max-height:420px;overflow:auto;">{{ $pretty ?? ($order->midtrans_payload ?? '-') }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header"><strong>Tindakan Cepat</strong></div>
                                    <div class="card-body d-grid gap-2">
                                        @if(($order->status ?? '') !== 'paid')
                                            <form method="POST" action="{{ route('admin.checkout.markPaid',$order->id) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-success">Tandai Paid</button>
                                            </form>
                                        @endif
                                        @if(($order->status ?? '') !== 'unpaid')
                                            <form method="POST" action="{{ route('admin.checkout.markUnpaid',$order->id) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-outline-secondary">Tandai Unpaid</button>
                                            </form>
                                        @endif
                                        @if ($order->payment_gateway === 'midtrans' && !empty($order->payment_redirect_url))
                                            <a href="{{ $order->payment_redirect_url }}" target="_blank" class="btn btn-outline-primary">Buka Link Pembayaran</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mt-3">
                                    <div class="card-header"><strong>Meta</strong></div>
                                    <div class="card-body">
                                        <div class="small text-muted">User ID</div>
                                        <div class="mb-2">{{ $order->user_id ?? '-' }}</div>
                                        <div class="small text-muted">Midtrans Order ID</div>
                                        <div class="mb-2">{{ $order->midtrans_order_id ?? '-' }}</div>
                                        <div class="small text-muted">Payment Token</div>
                                        <div class="mb-2"><code class="small">{{ $order->payment_token ?? '-' }}</code></div>
                                        <div class="small text-muted">Gross Amount</div>
                                        <div>{{ $order->currency ?? 'IDR' }} {{ number_format((float)($order->gross_amount ?? 0), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> {{-- /card-body --}}
                </div>
            </section>
        </div>

    </x-slot>
    
    @push('scripts')
    <script>
    function copyText(text, btn){
        navigator.clipboard.writeText(text).then(()=>{
            const t=btn.innerText; btn.innerText='Copied!';
            setTimeout(()=>btn.innerText=t, 1200);
        });
    }
    </script>
    @endpush
</x-mazer-layout>
