<x-mazer-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Tambah Produk Baru</h3>
            <p class="text-subtitle text-muted">Isi detail produk pada formulir di bawah ini.</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Formulir Produk</h4>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.products.store') }}">
                            @csrf
                            
                            {{-- Memuat field formulir dari file partial --}}
                            @include('admin.products._form')

                            {{-- Tombol Aksi Formulir --}}
                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save-fill"></i> Simpan Produk
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-light">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    
    </x-slot>
</x-mazer-layout>