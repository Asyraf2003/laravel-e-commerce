<x-app-layout>
    <div class="container py-5">
        {{-- Menampilkan notifikasi dari session --}}
        @if (session('status')) <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('status') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
        @if (session('info')) <div class="alert alert-info alert-dismissible fade show" role="alert">{{ session('info') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif
        @if ($errors->any()) <div class="alert alert-danger alert-dismissible fade show" role="alert"><ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> @endif

        <h1 class="mb-4">Keranjang Belanja</h1>

        @if($items->isNotEmpty())
            <div class="row g-4">
                {{-- Kolom Kiri: Daftar Item --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">Item di Keranjang</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($items as $item)
                                <div class="list-group-item p-3">
                                    <div class="row align-items-center">
                                        {{-- Gambar Produk --}}
                                        <div class="col-3 col-md-2">
                                            @if($item->product && $item->product->primaryImage)
                                                {{-- **DIPERBAIKI:** Menggunakan asset() dan properti 'image_path' yang benar --}}
                                                <img src="{{ asset('' . $item->product->primaryImage->image_path) }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                                            @else
                                                {{-- Placeholder jika tidak ada gambar --}}
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 100%; aspect-ratio: 1 / 1;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-image text-muted" viewBox="0 0 16 16">
                                                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                                        <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.773a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Detail Produk --}}
                                        <div class="col-9 col-md-4">
                                            <h6 class="mb-1 text-truncate">{{ $item->product->name }}</h6>
                                            <span class="fw-bold text-primary">{{ 'Rp' . number_format($item->price_each, 0, ',', '.') }}</span>
                                        </div>

                                        {{-- Kuantitas --}}
                                        <div class="col-7 col-md-3 mt-3 mt-md-0">
                                            <form method="POST" action="{{ route('user.cart.update', $item) }}" class="d-flex">
                                                @csrf
                                                @method('PATCH')
                                                <div class="input-group input-group-sm" style="max-width: 140px;">
                                                     <button class="btn btn-outline-secondary" type="submit" name="qty" value="{{ $item->qty - 1 }}" {{ $item->qty <= 1 ? 'disabled' : '' }}>-</button>
                                                     <input type="text" name="qty" class="form-control text-center" value="{{ $item->qty }}" readonly>
                                                     <button class="btn btn-outline-secondary" type="submit" name="qty" value="{{ $item->qty + 1 }}">+</button>
                                                </div>
                                            </form>
                                        </div>

                                        {{-- Subtotal & Hapus --}}
                                        <div class="col-5 col-md-3 text-end mt-3 mt-md-0">
                                            <span class="fw-bold d-block">{{ 'Rp' . number_format($item->line_total, 0, ',', '.') }}</span>
                                            <form method="POST" action="{{ route('user.cart.remove', $item) }}" onsubmit="return confirm('Anda yakin ingin menghapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0 mt-1">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Ringkasan --}}
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 1rem;">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h5 class="mb-0">Ringkasan Belanja</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold fs-5 text-primary">{{ $summary['subtotal_text'] }}</span>
                                </div>
                                <div class="d-grid">
                                    <a href="{{ route('user.checkout.show') }}" class="btn btn-primary btn-lg">Lanjut ke Pengiriman</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-cart3 text-muted mb-3" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <h3 class="mt-2">Keranjang Anda Kosong</h3>
                <p class="text-muted">Sepertinya Anda belum menambahkan item apa pun.</p>
                <a href="/" class="btn btn-primary mt-3">Mulai Belanja</a>
            </div>
        @endif
    </div>
</x-app-layout>

