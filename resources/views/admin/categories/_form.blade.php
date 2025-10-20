@php
    /** @var \App\Models\Category|null $category */
    $isEdit = isset($category);
@endphp

{{-- Menampilkan semua error validasi di bagian atas untuk referensi cepat --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h4 class="alert-heading">Terjadi Kesalahan</h4>
        <p>Silakan periksa isian formulir di bawah ini untuk memperbaiki error.</p>
    </div>
@endif

<div class="row">
    {{-- Kolom Kiri untuk field utama --}}
    <div class="col-12 col-md-8">
        <div class="mb-3">
            <label for="name" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug <small class="text-muted">(opsional, otomatis dari nama)</small></label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}">
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="parent_id" class="form-label">Parent</label>
            <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                <option value="">— Tanpa Parent (Root) —</option>
                @foreach ($parentOptions as $opt)
                    <option value="{{ $opt['id'] }}"
                        @selected((string)old('parent_id', $category->parent_id ?? '') === (string)$opt['id'])
                        @disabled($opt['disabled'])
                    >
                        {{ $opt['label'] }}
                        @if($opt['disabled']) (tidak valid) @endif
                    </option>
                @endforeach
            </select>
             @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $category->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Kolom Kanan untuk gambar dan pengaturan lainnya --}}
    <div class="col-12 col-md-4">
        <div class="mb-3">
            <label for="image" class="form-label">Gambar (jpg/png/webp maks 2MB)</label>
            <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if($isEdit && !empty($category->image))
                <div class="mt-3">
                    <small class="text-muted d-block mb-2">Gambar saat ini:</small>
                    <img src="{{ asset('storage/'.$category->image) }}" alt="Gambar kategori" class="img-thumbnail" style="max-height: 100px;">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            @error('sort_order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
            <label class="form-check-label" for="is_active">Aktifkan Kategori</label>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const nameInput = document.querySelector('#name');
  const slugInput = document.querySelector('#slug');

  // Hanya jalankan jika slug kosong saat halaman dimuat
  if (nameInput && slugInput && !slugInput.value) {
    nameInput.addEventListener('input', () => {
      // Buat slug dari nilai input nama
      slugInput.value = nameInput.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '') // Hapus karakter non-alfanumerik kecuali spasi dan strip
        .replace(/\s+/g, '-')       // Ganti spasi dengan strip
        .replace(/-+/g, '-');        // Ganti beberapa strip dengan satu strip
    });
  }
});
</script>
@endpush