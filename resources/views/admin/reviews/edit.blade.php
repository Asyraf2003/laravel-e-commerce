<x-mazer-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Edit Review #{{ $review->id }}</h3>
            <p class="text-subtitle text-muted">Kelola rating, konten, dan status ulasan.</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Formulir Edit Ulasan</h4>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                    <div class="card-body">

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

                        {{-- Panel Info --}}
                        <div class="p-3 bg-light rounded mb-4">
                            <div class="row">
                                <div class="col-md-6"><strong>Produk:</strong> {{ $review->product?->name ?? '—' }}</div>
                                <div class="col-md-6"><strong>User:</strong> {{ $review->user?->name ?? '—' }}</div>
                            </div>
                        </div>

                        {{-- Formulir Edit Utama --}}
                        <form method="POST" action="{{ route('admin.reviews.update', $review) }}" id="review-update-form">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating (1-5)</label>
                                        <input type="number" name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror" min="1" max="5" value="{{ old('rating', $review->rating) }}" required>
                                        @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Judul</label>
                                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $review->title) }}" maxlength="150">
                                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="body" class="form-label">Isi Ulasan</label>
                                <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror" rows="6">{{ old('body', $review->body) }}</textarea>
                                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Ubah Status</label>
                                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">— Biarkan ({{ $review->status }}) —</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rejected_reason" class="form-label">Alasan Penolakan</label>
                                        <input type="text" name="rejected_reason" id="rejected_reason" class="form-control @error('rejected_reason') is-invalid @enderror" value="{{ old('rejected_reason', $review->rejected_reason) }}" maxlength="255">
                                        <div class="form-text">Diisi jika status diubah menjadi "Rejected".</div>
                                        @error('rejected_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- Tombol Aksi --}}
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                            {{-- Tombol Simpan (Kiri) --}}
                            <button type="submit" class="btn btn-primary" form="review-update-form">
                                <i class="bi bi-save-fill"></i> Simpan Perubahan
                            </button>

                            {{-- Aksi Cepat (Kanan) --}}
                            <div class="d-flex flex-wrap gap-2">
                                @can('update', $review)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <div class="input-group">
                                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Alasan penolakan..." style="min-width: 200px;">
                                            <button type="submit" class="btn btn-warning"><i class="bi bi-x-circle"></i> Reject</button>
                                        </div>
                                    </form>
                                @endcan
                                @can('delete', $review)
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Anda yakin ingin menghapus ulasan ini secara permanen?');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash-fill"></i> Hapus</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Link ke Votes --}}
                <div class="mt-4">
                    <a href="{{ route('admin.reviews.votes.index', $review) }}" class="btn btn-outline-info">
                        <i class="bi bi-hand-thumbs-up"></i> Lihat Votes untuk Ulasan Ini
                    </a>
                </div>
            </section>
        </div>

    </x-slot>
</x-mazer-layout>