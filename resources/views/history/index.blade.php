<x-app-layout>
    <div class="container py-5">
        <h1 class="mb-4">Riwayat Pesanan Saya</h1>

        @if (session('status')) <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('status') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
        @if (session('error')) <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @forelse ($orders as $order)
                    <div class="list-group-item p-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-1">Nomor Pesanan</h6>
                                <span class="fw-bold text-primary">{{ $order->order_no }}</span>
                            </div>
                            <div class="col-md-3">
                                <h6 class="mb-1">Tanggal Pesanan</h6>
                                <span class="text-muted">{{ $order->placed_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="col-md-2">
                                <h6 class="mb-1">Total Pembayaran</h6>
                                <span class="fw-bold">{{ 'Rp' . number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-md-2 text-md-center">
                                @php
                                    $statusClass = match($order->status) {
                                        'pending_payment' => 'bg-warning text-dark',
                                        'paid' => 'bg-success',
                                        'shipped' => 'bg-info text-dark',
                                        'delivered' => 'bg-primary',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} fs-6">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                            <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                {{-- **DIPERBAIKI:** Menambahkan tombol "Refresh" dan menyusun ulang tombol aksi --}}
                                <div class="d-flex flex-column flex-md-row justify-content-end gap-2">
                                    @if($order->status == \App\Models\Order::STATUS_PENDING_PAYMENT)
                                        @if($order->payment_redirect_url)
                                            <a href="{{ $order->payment_redirect_url }}" class="btn btn-sm btn-success">
                                                Bayar
                                            </a>
                                        @endif
                                        {{-- Tombol untuk memeriksa status pembayaran secara manual --}}
                                        <a href="{{ route('user.orders.refresh', $order) }}" class="btn btn-sm btn-outline-info" title="Cek Status Pembayaran">
                                            Refresh
                                        </a>
                                    @elseif($order->status == \App\Models\Order::STATUS_SHIPPED && $order->awb)
                                        <a href="#" class="btn btn-sm btn-info">
                                            Lacak
                                        </a>
                                    @endif
                                    <a href="{{ route('user.history.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 border-top pt-3">
                            <small class="text-muted">Item:</small>
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                @foreach($order->items->take(5) as $item)
                                    <div data-bs-toggle="tooltip" title="{{ $item->qty }}x {{ $item->product_name_snapshot }}">
                                        @if($item->product && $item->product->primaryImage)
                                            {{-- **DIPERBAIKI:** Menggunakan asset() dan properti 'image_path' --}}
                                            <img src="{{ asset('' . $item->product->primaryImage->image_path) }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->product_name_snapshot }}">
                                        @else
                                            {{-- Placeholder jika tidak ada gambar --}}
                                            <div class="d-flex align-items-center justify-content-center bg-light rounded img-thumbnail" style="width: 50px; height: 50px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image text-muted" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.773a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($order->items->count() > 5)
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 50px; height: 50px;">
                                        <span class="fw-bold text-muted" style="font-size: 0.8rem;">+{{ $order->items->count() - 5 }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-5">
                        <p class="fs-5 text-muted">Anda belum memiliki riwayat pesanan.</p>
                        <a href="/" class="btn btn-primary mt-2">Mulai Belanja</a>
                    </div>
                @endforelse
            </div>
            
            @if($orders->hasPages())
                <div class="card-footer">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            });
        </script>
    @endpush
</x-app-layout>

