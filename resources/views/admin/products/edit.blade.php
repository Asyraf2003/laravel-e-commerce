<x-mazer-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Edit Produk: {{ $product->name }}</h3>
            <p class="text-subtitle text-muted">Perbarui detail untuk produk ini.</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Formulir Edit Produk</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                            </a>
                            @can('update', $product)
                                <a href="{{ route('admin.products.images.index', $product) }}" class="btn btn-info">
                                    <i class="bi bi-images"></i> Kelola Gambar
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Pesan Status Sesi --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.products.update', $product) }}" id="product-update-form">
                            @csrf
                            @method('PATCH')
                            
                            {{-- Memuat field formulir dari file partial --}}
                            @include('admin.products._form', ['product' => $product])
                        </form>

                        {{-- Tombol Aksi Formulir --}}
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            {{-- Tombol Update (di kiri) --}}
                            <button type="submit" class="btn btn-primary" form="product-update-form">
                                <i class="bi bi-save-fill"></i> Update Produk
                            </button>
                            
                            {{-- Tombol Hapus (di kanan) --}}
                            @can('delete', $product)
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                onsubmit="return confirm('Anda yakin ingin menghapus produk ini secara permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </button>
                            </form>
                            @endcan
                        </div>

                    </div>
                </div>
            </section>
        </div>

    </x-slot>
</x-mazer-layout>