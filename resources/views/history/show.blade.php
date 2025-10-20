<x-app-layout>
    <div class="container py-5">
        @if (session('status')) <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('status') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
        @if (session('error')) <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Detail Pesanan</h1>
                <p class="text-muted">Pesanan #{{ $order->order_no }}</p>
            </div>
            <a href="{{ route('user.history.index') }}" class="btn btn-outline-secondary">
                &larr; Kembali ke Riwayat
            </a>
        </div>

        <div class="row g-4">
            {{-- Kolom Kiri: Detail Pengiriman & Status --}}
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h5 class="mb-0">Status Pesanan</h5></div>
                    <div class="card-body text-center">
                         @php
                            $statusClass = match($order->status) {
                                'pending_payment' => 'bg-warning text-dark', 'paid' => 'bg-success',
                                'shipped' => 'bg-info text-dark', 'delivered' => 'bg-primary',
                                'cancelled' => 'bg-danger', default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} fs-5">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span>
                        <p class="text-muted mt-2 mb-1">Dipesan pada: {{ $order->placed_at->format('d M Y, H:i') }}</p>

                        {{-- Menampilkan tanggal pembayaran jika sudah lunas --}}
                        @if($order->is_paid && $order->paid_at)
                            <p class="text-muted small">Dibayar pada: {{ $order->paid_at->format('d M Y, H:i') }}</p>
                        @endif

                        @if($order->status == \App\Models\Order::STATUS_PENDING_PAYMENT)
                            @if($order->payment_redirect_url)
                                <a href="{{ $order->payment_redirect_url }}" class="btn btn-success w-100 mt-3">Lanjutkan Pembayaran</a>
                            @endif
                            <a href="{{ route('user.orders.refresh', $order) }}" class="btn btn-outline-info w-100 mt-2">Cek Status Pembayaran</a>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Alamat Pengiriman</h5></div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->recipient_name }}</strong></p>
                        <p class="mb-1 text-muted">{{ $order->recipient_phone }}</p>
                        <p class="mb-0 text-muted">
                            {{ $order->address }},<br>
                            {{ $order->subdistrict_name }}, {{ $order->city_name }},<br>
                            {{ $order->province_name }} {{ $order->postal_code }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Rincian Item & Biaya --}}
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Rincian Pesanan</h5></div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($order->items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ asset('/' . $item->product->primaryImage->image_path) }}" class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->product_name_snapshot }}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded img-thumbnail me-3" style="width: 60px; height: 60px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-image text-muted" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.773a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $item->product_name_snapshot }}</div>
                                        <small class="text-muted">{{ $item->qty }} x {{ 'Rp' . number_format($item->price_each, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                                <span class="fw-bold">{{ 'Rp' . number_format($item->line_total, 0, ',', '.') }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-light p-3">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                <span>Subtotal</span>
                                <span>{{ 'Rp' . number_format($order->subtotal, 0, ',', '.') }}</span>
                            </li>
                             <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                <span>Ongkos Kirim ({{ strtoupper($order->courier) }} - {{ $order->service }})</span>
                                <span>{{ 'Rp' . number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent fw-bold fs-5 border-top pt-3">
                                <span>Grand Total</span>
                                <span class="text-primary">{{ 'Rp' . number_format($order->total, 0, ',', '.') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

