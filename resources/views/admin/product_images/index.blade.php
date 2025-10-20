<x-mazer-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Images for: {{ $product->name }}</h3>
            <p class="text-subtitle text-muted">Kelola galeri gambar untuk produk ini.</p>
        </div>

        <div class="page-content">
            {{-- Pesan Status dan Error --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Terjadi beberapa masalah:</h4>
                    <ul>
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tombol Navigasi --}}
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Produk</a>
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-light"><i class="bi bi-pencil-square"></i> Edit Detail Produk</a>
            </div>

            <section class="section">
                <div class="row">
                    {{-- Kolom untuk form upload --}}
                    <div class="col-12 col-lg-4">
                        @can('update', $product)
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Upload Gambar Baru</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Pilih Gambar (bisa multiple)</label>
                                        <input class="form-control" type="file" id="images" name="images[]" accept="image/*" multiple required>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
                                </form>
                            </div>
                        </div>
                        @endcan
                    </div>

                    {{-- Kolom untuk daftar gambar --}}
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Galeri Gambar</h4>
                            </div>
                            <div class="card-body">
                                {{-- Panel Reorder Cepat (Bulk) --}}
                                @if($images->count() > 1)
                                <div class="p-3 bg-light rounded mb-4">
                                    <form method="POST" action="{{ route('admin.products.images.reorder', $product) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="d-flex gap-3 align-items-center flex-wrap">
                                            <strong class="me-2">Bulk Reorder:</strong>
                                            @foreach ($images as $img)
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <span class="input-group-text">#{{ $img->id }}</span>
                                                <input type="number" name="orders[{{ $img->id }}]" value="{{ $img->sort_order }}" class="form-control">
                                            </div>
                                            @endforeach
                                            <button type="submit" class="btn btn-sm btn-primary">Update Urutan</button>
                                        </div>
                                    </form>
                                </div>
                                @endif

                                {{-- Tabel Daftar Gambar --}}
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Preview</th>
                                                <th>Info</th>
                                                <th class="text-center">Primary</th>
                                                <th>Edit Cepat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($images as $img)
                                                <tr>
                                                    <td>
                                                        <img src="{{ asset(''.$img->image_path) }}" alt="{{ $img->alt_text }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                                    </td>
                                                    <td class="small">
                                                        <div><strong>ID:</strong> {{ $img->id }}</div>
                                                        <div><strong>Order:</strong> {{ $img->sort_order }}</div>
                                                        <div><strong>Alt:</strong> {{ $img->alt_text ?: '—' }}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($img->is_primary)
                                                            <span class="badge bg-success">PRIMARY</span>
                                                        @else
                                                            <form method="POST" action="{{ route('admin.products.images.primary', [$product, $img]) }}">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-success">Set Primary</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <form method="POST" action="{{ route('admin.products.images.update', [$product, $img]) }}">
                                                            @csrf @method('PATCH')
                                                            <div class="d-grid gap-2">
                                                                <input type="text" name="alt_text" value="{{ old('alt_text', $img->alt_text) }}" class="form-control form-control-sm" placeholder="Alt text" maxlength="255">
                                                                <input type="number" name="sort_order" value="{{ old('sort_order', $img->sort_order) }}" class="form-control form-control-sm" placeholder="Sort order" min="0">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="is_primary_{{$img->id}}">
                                                                    <label class="form-check-label small" for="is_primary_{{$img->id}}">Juga jadikan Primary</label>
                                                                </div>
                                                                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" onsubmit="return confirm('Anda yakin ingin menghapus gambar ini?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="alert alert-light text-center my-3">Belum ada gambar yang diunggah.</div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
    </x-slot>
</x-mazer-layout>