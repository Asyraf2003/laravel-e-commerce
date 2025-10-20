@php
    /** @var \App\Models\Product|null $product */
    $isEdit = isset($product);
    $pubVal = old('published_at', $isEdit && $product->published_at ? $product->published_at->format('Y-m-d\TH:i') : '');
@endphp

{{-- Menampilkan semua error validasi di bagian atas untuk referensi cepat --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h4 class="alert-heading">Terjadi Kesalahan</h4>
        <p>Silakan periksa isian formulir di bawah ini untuk memperbaiki error.</p>
    </div>
@endif

<div class="row">
    {{-- KOLOM KIRI (INFORMASI UTAMA) --}}
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Produk</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <small class="text-muted">(opsional, dibuat otomatis)</small></label>
                            <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $product->slug ?? '') }}">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU <small class="text-muted">(opsional)</small></label>
                            <input type="text" id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku ?? '') }}">
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="short_desc" class="form-label">Deskripsi Singkat</label>
                    <textarea id="short_desc" name="short_desc" class="form-control @error('short_desc') is-invalid @enderror" rows="2">{{ old('short_desc', $product->short_desc ?? '') }}</textarea>
                    @error('short_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="long_desc" class="form-label">Deskripsi Lengkap</label>
                    <textarea id="long_desc" name="long_desc" class="form-control @error('long_desc') is-invalid @enderror" rows="6">{{ old('long_desc', $product->long_desc ?? '') }}</textarea>
                    @error('long_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <h6 class="mt-4">Harga & Stok</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="original_price" class="form-label">Harga Asli (Rp)</label>
                            <input type="number" id="original_price" name="original_price" class="form-control @error('original_price') is-invalid @enderror" min="0" step="1" value="{{ old('original_price', $product->original_price ?? 0) }}" required>
                             @error('original_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="discount_percent" class="form-label">Diskon (%)</label>
                            <input type="number" id="discount_percent" name="discount_percent" class="form-control @error('discount_percent') is-invalid @enderror" min="0" max="100" step="1" value="{{ old('discount_percent', $product->discount_percent ?? 0) }}">
                             @error('discount_percent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="final_price" class="form-label">Harga Final</label>
                            <input type="text" id="final_price" class="form-control" value="{{ old('final_price', $product->final_price ?? 0) }}" readonly>
                            <div class="form-text">Dihitung otomatis</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok</label>
                            <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror" min="0" step="1" value="{{ old('stock', $product->stock ?? 0) }}">
                             @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="weight" class="form-label">Berat (gram)</label>
                            <input type="number" id="weight" name="weight" class="form-control @error('weight') is-invalid @enderror" min="0" step="1" value="{{ old('weight', $product->weight ?? 0) }}">
                             @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- KOLOM KANAN (PENGATURAN) --}}
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pengaturan</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach ($categoryOptions as $opt)
                            <option value="{{ $opt['id'] }}" @selected((string)old('category_id', $product->category_id ?? '') === (string)$opt['id'])>
                                {{ $opt['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="published_at" class="form-label">Tanggal Publikasi</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ $pubVal }}">
                    @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr>
                <h6>Status & Tanda</h6>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))>
                    <label class="form-check-label" for="is_featured">Featured</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_best_seller" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller ?? false))>
                    <label class="form-check-label" for="is_best_seller">Best Seller</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_new" name="is_new" value="1" @checked(old('is_new', $product->is_new ?? false))>
                    <label class="form-check-label" for="is_new">Produk Baru</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_hot" name="is_hot" value="1" @checked(old('is_hot', $product->is_hot ?? false))>
                    <label class="form-check-label" for="is_hot">Produk Hot</label>
                </div>
                
                <hr>
                <h6>Opsi Berbagi</h6>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="share_fb" name="share_fb" value="1" @checked(old('share_fb', $product->share_fb ?? false))>
                    <label class="form-check-label" for="share_fb">Bagikan ke Facebook</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="share_x" name="share_x" value="1" @checked(old('share_x', $product->share_x ?? false))>
                    <label class="form-check-label" for="share_x">Bagikan ke X (Twitter)</label>
                </div>
                 <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="share_wa" name="share_wa" value="1" @checked(old('share_wa', $product->share_wa ?? false))>
                    <label class="form-check-label" for="share_wa">Bagikan ke WhatsApp</label>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const nameInput = document.querySelector('#name');
  const slugInput = document.querySelector('#slug');
  const priceInput = document.querySelector('#original_price');
  const discInput = document.querySelector('#discount_percent');
  const finalPriceInput = document.querySelector('#final_price');

  // Auto-generate slug from name if slug is empty
  if (nameInput && slugInput && !slugInput.value) {
    nameInput.addEventListener('input', () => {
      slugInput.value = nameInput.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '') // remove non-alphanumeric chars except dashes and spaces
        .replace(/\s+/g, '-')       // replace spaces with dashes
        .replace(/-+/g, '-');        // replace multiple dashes with a single one
    });
  }

  // Recompute final price
  const recomputeFinalPrice = () => {
    if (!priceInput || !discInput || !finalPriceInput) return;
    
    const price = parseInt(priceInput.value || '0', 10);
    const discount = Math.min(100, Math.max(0, parseInt(discInput.value || '0', 10)));
    const finalPrice = Math.round(price * (100 - discount) / 100);
    
    // Format as currency for display
    finalPriceInput.value = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(finalPrice);
  };
  
  if (priceInput) priceInput.addEventListener('input', recomputeFinalPrice);
  if (discInput) discInput.addEventListener('input', recomputeFinalPrice);
  
  // Initial computation on page load
  recomputeFinalPrice();
});
</script>
@endpush