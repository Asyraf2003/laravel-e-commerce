<x-mazer-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot>
        
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Keranjang Belanja Pengguna</h3>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Detail Keranjang untuk: {{ $user->name }}</h4>
                        <p class="text-muted mb-0">{{ $user->email }} (ID: {{ $user->id }})</p>
                    </div>
                    <div class="card-body">

                        {{-- Pesan Status Sesi --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Error Validasi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4 class="alert-heading">Terjadi beberapa masalah.</h4>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Ringkasan dan Tombol Aksi --}}
                        <div class="p-3 mb-4 bg-light rounded">
                            <h6 class="mb-2">Ringkasan Keranjang</h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Total Item:</strong> {{ $summary['items_count'] }}</li>
                                <li><strong>Subtotal:</strong> {{ $summary['subtotal_text'] }}</li>
                                <li><strong>Estimasi Berat:</strong> {{ number_format($summary['weight'], 0, ',', '.') }} gram</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom">
                            <form method="POST" action="{{ route('admin.carts.user.clear', $user) }}" onsubmit="return confirm('Anda yakin ingin mengosongkan semua item `in_cart` milik pengguna ini?')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    Kosongkan Keranjang
                                </button>
                            </form>
                            <a href="{{ route('admin.carts.index') }}" class="btn btn-secondary">Kembali ke Daftar Keranjang</a>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Dashboard</a>
                        </div>


                        {{-- Tabel Item Keranjang --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Produk</th>
                                        <th class="text-center">Kuantitas</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $it)
                                        <tr>
                                            <td>#{{ $it->id }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $it->product_name_snapshot ?? $it->product?->name ?? '-' }}</div>
                                                <small class="text-muted">Product ID: {{ $it->product_id ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.carts.item.qty', $it) }}" class="d-flex gap-2 justify-content-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="qty" value="{{ (int) $it->qty }}" min="1" class="form-control form-control-sm" style="width: 80px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                                </form>
                                            </td>
                                            <td class="text-end text-nowrap">Rp{{ number_format((int) $it->price_each, 0, ',', '.') }}</td>
                                            <td class="text-end text-nowrap fw-bold">Rp{{ number_format((int) $it->line_total, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $status_color = match($it->status) {
                                                        'in_cart' => 'primary',
                                                        'saved' => 'info',
                                                        'checked_out' => 'success',
                                                        'removed' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-light-{{$status_color}} mb-2 d-block">{{ ucfirst(str_replace('_', ' ', $it->status)) }}</span>
                                                <form method="POST" action="{{ route('admin.carts.item.status', $it) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="input-group input-group-sm">
                                                        <select name="status" class="form-select">
                                                            @foreach (['in_cart','saved','removed'] as $st)
                                                                <option value="{{ $st }}" @selected($it->status === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-outline-secondary">Set</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.carts.item.destroy', $it) }}"
                                                    onsubmit="return confirm('Tindakan ini akan mengubah status item menjadi `removed`. Lanjutkan?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <div class="alert alert-light text-center my-3" role="alert">
                                                    Keranjang pengguna ini kosong.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </x-slot>
</x-mazer-layout>