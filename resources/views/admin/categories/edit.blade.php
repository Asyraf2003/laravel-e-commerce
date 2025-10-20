<x-mazer-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Edit Kategori: {{ $category->name }}</h3>
            <p class="text-subtitle text-muted">Perbarui detail untuk kategori ini.</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Formulir Edit Kategori</h4>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- Pesan Status Sesi --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" id="category-update-form">
                            @csrf
                            @method('PATCH')
                            
                            {{-- Memuat field formulir dari file partial --}}
                            @include('admin.categories._form', ['category' => $category])
                        </form>

                        {{-- Tombol Aksi Formulir --}}
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            {{-- Tombol Update (di kiri) --}}
                            <button type="submit" class="btn btn-primary" form="category-update-form">
                                <i class="bi bi-save-fill"></i> Update Kategori
                            </button>
                            
                            {{-- Tombol Hapus (di kanan) --}}
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Anda yakin ingin menghapus kategori ini secara permanen? Ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </x-slot>
</x-mazer-layout>