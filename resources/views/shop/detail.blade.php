<x-app-layout>
    <x-slot>
        <div class="container-fluid shop py-5">
            <div class="container py-5">
                <div class="row g-4">
                    <div class="col-lg-7 col-xl-9 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="row g-4 single-product">
                            <div class="col-xl-6">
                                <div class="single-carousel owl-carousel">
                                    @php
                                    // Kumpulkan semua gambar dari relasi
                                    $images = collect($product->images ?? [])
                                        ->filter(fn($img) => filled($img->image_path));

                                    // Pastikan primary muncul dulu (ambil dari relasi primaryImage kalau ada)
                                    if ($product->primaryImage && filled($product->primaryImage->image_path)) {
                                        $images = collect([$product->primaryImage])
                                            ->merge(
                                                $images->filter(fn($img) => $img->id !== $product->primaryImage->id)
                                            );
                                    }

                                    // Urutkan: primary desc, sort_order asc (null => paling belakang), lalu id asc
                                    $images = $images->values()->sort(function ($a, $b) {
                                        if ($a->is_primary !== $b->is_primary) {
                                            return $a->is_primary ? -1 : 1;
                                        }
                                        $ao = is_null($a->sort_order) ? PHP_INT_MAX : (int)$a->sort_order;
                                        $bo = is_null($b->sort_order) ? PHP_INT_MAX : (int)$b->sort_order;
                                        if ($ao !== $bo) return $ao <=> $bo;
                                        return (int)$a->id <=> (int)$b->id;
                                    });

                                    // Hilangkan duplikasi berdasarkan path
                                    $images = $images->unique('image_path')->values();

                                    // Helper buat URL final
                                    $toSrc = function (string $path): string {
                                        return \Illuminate\Support\Str::startsWith($path, ['http://','https://'])
                                            ? $path
                                            : asset(''.$path);
                                    };
                                    @endphp

                                    @forelse ($images as $img)
                                    @php
                                        $src = $toSrc($img->image_path);
                                        $alt = $img->alt_text ?: ($product->name ?? 'Image');
                                    @endphp

                                    <div class="single-item" data-dot="<img class='img-fluid' src='{{ $src }}' alt='{{ e($alt) }}'>">
                                        <div class="single-inner bg-light rounded">
                                        <img src="{{ $src }}" class="img-fluid rounded" alt="{{ e($alt) }}">
                                        </div>
                                    </div>
                                    @empty
                                    @php $ph = asset('img/placeholder.png'); @endphp
                                    <div class="single-item" data-dot="<img class='img-fluid' src='{{ $ph }}' alt='No image'>">
                                        <div class="single-inner bg-light rounded">
                                        <img src="{{ $ph }}" class="img-fluid rounded" alt="No image">
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-xl-6">
                                @if(session('status'))
                                <div class="alert alert-success mt-2">{{ session('status') }}</div>
                                @endif

                                <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                                <p class="mb-3">Category: {{ $product->category->name ?? 'Uncategorized' }}</p>

                                @if($product->has_discount)
                                    <h5 class="fw-bold text-danger mb-2">{{ $product->discount_price_formatted }}</h5>
                                    <p class="text-muted text-decoration-line-through mb-0">
                                        {{ $product->original_price_formatted }}
                                    </p>
                                    <small class="text-success fw-semibold">
                                        Hemat {{ (int) $product->discount_percent }}% ({{ $product->saved_amount_formatted }})
                                    </small>
                                @else
                                    <h5 class="fw-bold mb-3">{{ $product->original_price_formatted }}</h5>
                                @endif

                                @php
                                    // Pastikan float 0..5
                                    $avg = max(0, min(5, (float)($avgRating ?? 0)));

                                    $full = floor($avg);              // jumlah bintang penuh
                                    $frac = $avg - $full;             // pecahan
                                    // Ambang pecahan: <0.25 kosong, 0.25–0.75 setengah, ≥0.75 jadi penuh
                                    if ($frac >= 0.75) { $full++; $half = 0; }
                                    elseif ($frac >= 0.25) { $half = 1; }
                                    else { $half = 0; }

                                    $empty = max(0, 5 - $full - $half); // sisa bintang kosong
                                @endphp

                                <div class="d-flex align-items-center mb-4" title="{{ number_format($avg,1) }} / 5">
                                    {{-- FULL stars --}}
                                    @for ($i = 0; $i < $full; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor

                                    {{-- HALF star --}}
                                    @if ($half)
                                        {{-- Font Awesome 5: fa-star-half-alt  |  Font Awesome 6: fa-star-half-stroke --}}
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @endif

                                    {{-- EMPTY stars --}}
                                    @for ($i = 0; $i < $empty; $i++)
                                        {{-- FA5: far fa-star (kosong). Jika pakai FA4 shim, 'fa-star-o' juga jalan --}}
                                        <i class="far fa-star text-warning"></i>
                                    @endfor

                                    <small class="ms-2">
                                        {{ number_format($avg,1) }} ({{ $reviewsCount }} reviews)
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                        target="_blank" rel="noopener" class="btn btn-primary d-inline-block rounded text-white py-1 px-4 me-1">
                                        <i class="fab fa-facebook-f me-1"></i> Share
                                    </a>

                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(url()->current()) }}"
                                        target="_blank" rel="noopener" class="btn btn-secondary d-inline-block rounded text-white py-1 px-4 mx-1">
                                        <i class="fab fa-twitter me-1"></i> Share
                                    </a>

                                    <a href="https://api.whatsapp.com/send?text={{ urlencode('Lihat produk ini: ' . $product->name . ' ' . url()->current()) }}"
                                        target="_blank" rel="noopener" class="btn btn-success d-inline-block rounded text-white py-1 px-4 mx-1">
                                        <i class="fab fa-whatsapp me-1"></i> Share
                                    </a>
                                </div>

                                <div class="d-flex flex-column mb-3">
                                    <small>Product SKU: {{ $product->sku ?? 'N/A' }}</small>
                                    <small>Available:
                                        <strong class="text-primary">
                                            {{ $product->stock > 0 ? $product->stock.' items in stock' : 'Out of stock' }}
                                        </strong>
                                    </small>
                                </div>

                                @php
                                    $short = $product->short_desc ?: \Illuminate\Support\Str::limit($product->description ?? '', 160);
                                @endphp
                                <p class="mb-4">{!! nl2br(e($short)) !!}</p>

                                @if (!empty($product->description) && empty($product->short_desc))
                                    <p class="mb-4">{!! nl2br(e(\Illuminate\Support\Str::of($product->description ?? '')->substr(160, 400))) !!}</p>
                                @endif

                                @auth
                                    {{-- ACTION BAR: Cart (modal qty), Wishlist (AJAX), Checkout (dummy) --}}
                                    <div class="d-flex align-items-center gap-2 mb-3">

                                        {{-- CART (opens modal) --}}
                                        <button type="button"
                                            class="btn btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:44px;height:44px"
                                            data-bs-toggle="modal" data-bs-target="#qtyModal"
                                            aria-label="Tambah ke keranjang">
                                            <i class="fa fa-shopping-bag"></i>
                                        </button>

                                        {{-- WISHLIST (AJAX toggle) --}}
                                        @php
                                            $__inWishlist = auth()->user()->wishlists()
                                                ->where('product_id', $product->id)
                                                ->exists();
                                        @endphp
                                        <button type="button" id="btnWishlist"
                                            class="btn rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:44px;height:44px;background-color:{{ $__inWishlist ? '#dc3545' : '#ffffff' }};border:2px solid #dc3545;transition:all .2s ease;"
                                            aria-pressed="{{ $__inWishlist ? 'true' : 'false' }}"
                                            aria-label="{{ $__inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                                            title="{{ $__inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                                            data-toggle-url="{{ route('user.wishlist.toggle', $product) }}"
                                            data-initial="{{ $__inWishlist ? '1' : '0' }}">
                                            <i class="fa fa-heart" style="font-size:18px;color:{{ $__inWishlist ? '#ffffff' : '#dc3545' }};"></i>
                                        </button>

                                        {{-- CHECKOUT (dummy) --}}
                                        <a href="#"
                                        class="btn btn-outline-success rounded-pill px-3 py-2 d-inline-flex align-items-center"
                                        aria-label="Checkout sekarang">
                                            <i class="fa fa-credit-card me-2"></i> Checkout
                                        </a>
                                    </div>
                                @else
                                    {{-- Tidak login: arahkan ke login --}}
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <a href="{{ route('login') }}"
                                        class="btn btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width:44px;height:44px" title="Login untuk menambah ke keranjang">
                                            <i class="fa fa-shopping-bag"></i>
                                        </a>
                                        <a href="{{ route('login') }}"
                                        class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width:44px;height:44px" title="Login untuk wishlist">
                                            <i class="fa fa-heart-o"></i>
                                        </a>
                                        <a href="{{ route('login') }}"
                                        class="btn btn-outline-success rounded-pill px-3 py-2 d-inline-flex align-items-center">
                                            <i class="fa fa-credit-card me-2"></i> Checkout
                                        </a>
                                    </div>
                                @endauth
                            </div>
                            <div class="col-lg-12">
                                <nav>
                                    <div class="nav nav-tabs mb-3">
                                        <button class="nav-link active border-white border-bottom-0" type="button"
                                            role="tab" id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                            aria-controls="nav-about" aria-selected="true">Description</button>
                                        <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                            id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews"
                                            aria-controls="nav-reviews" aria-selected="false">
                                            Reviews ({{ $reviewsCount }})
                                        </button>
                                    </div>
                                </nav>

                                <div class="tab-content mb-5">
                                    <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                        {!! nl2br(e($product->long_desc ?? 'Belum ada deskripsi produk.')) !!}
                                    </div>

                                    <div class="tab-pane" id="nav-reviews" role="tabpanel" aria-labelledby="nav-reviews-tab">
                                        @forelse ($reviews as $rev)
                                            <div class="d-flex mb-3">
                                                <img src="{{ asset('assets/img/avatar.jpg') }}" class="img-fluid rounded-circle p-3"
                                                    style="width: 80px; height: 80px;" alt="avatar">
                                                <div>
                                                    <p class="mb-1" style="font-size: 13px;">{{ $rev->created_at->format('F d, Y') }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">{{ $rev->user->name ?? 'Anonymous' }}</h6>
                                                        <div class="d-flex ms-3">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="fa fa-star {{ $i <= ($rev->rating ?? 0) ? 'text-secondary' : '' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p class="mb-0">{{ $rev->body }}</p>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="alert alert-secondary">Belum ada review.</div>
                                        @endforelse

                                        <div class="mt-3">
                                            {{ $reviews->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> 
                </div>
            </div>
        </div>
        @auth
        <!-- Qty Modal -->
        <div class="modal fade" id="qtyModal" tabindex="-1" aria-labelledby="qtyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('user.cart.add', $product) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="qtyModalLabel">Tambah ke Keranjang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                @php
                    $unitPrice = (int) ($product->final_price ?? $product->price ?? 0);
                    $fmt = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
                    $unitPriceFormatted = $fmt->formatCurrency($unitPrice, 'IDR'); // "Rp ..."
                @endphp

                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="me-2">Stok:</span>
                            <span class="badge bg-primary">{{ $product->stock }}</span>
                        </div>
                        <div>
                            <small class="text-muted">Harga satuan:</small>
                            <strong class="ms-2">{{ $unitPriceFormatted }}</strong>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="me-3">Jumlah</span>
                        <div class="input-group" style="width: 180px;">
                            <button type="button" id="btnQtyMinus" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-minus"></i>
                            </button>

                            <input
                                id="qtyInput"
                                name="qty"
                                type="number"
                                class="form-control form-control-sm text-center"
                                value="1"
                                min="1"
                                max="{{ max(1, (int) $product->stock) }}"
                                data-max="{{ (int) $product->stock }}"
                                data-price="{{ $unitPrice }}"
                                inputmode="numeric"
                                pattern="[0-9]*"
                            >

                            <button type="button" id="btnQtyPlus" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Total</small>
                        <strong id="totalPrice">{{ $unitPriceFormatted }}</strong>
                    </div>

                    @if($product->stock <= 0)
                        <div class="alert alert-warning mt-3 mb-0">Stok habis. Tidak dapat menambahkan ke keranjang.</div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnAddCart" class="btn btn-primary" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        <i class="fa fa-shopping-bag me-2"></i> Tambahkan
                    </button>
                </div>
            </form>
        </div>
        </div>
        @endauth
    </x-slot>
@push('scripts')
<script>
    // --- UTILITIES (Bisa digunakan oleh fungsi lain) ---

    /**
     * Menampilkan notifikasi toast sederhana di atas layar.
     * @param {string} message - Pesan yang akan ditampilkan.
     * @param {boolean} [isError=false] - Jika true, toast akan berwarna merah.
     */
    function showToast(message, isError = false) {
        const wrap = document.createElement('div');
        wrap.className = `alert ${isError ? 'alert-danger' : 'alert-success'} position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm`;
        wrap.style.zIndex = 9999;
        wrap.textContent = message;
        document.body.appendChild(wrap);

        // Sedikit animasi fade-in sederhana
        setTimeout(() => {
            wrap.style.opacity = '1';
            wrap.style.transform = 'translate(-50%, 0)';
        }, 20);
        
        // Hapus toast setelah beberapa detik
        setTimeout(() => {
            wrap.style.opacity = '0';
            wrap.addEventListener('transitionend', () => wrap.remove());
        }, 2500);
    }


    // --- MAIN SCRIPT (Jalankan setelah halaman siap) ---

    document.addEventListener('DOMContentLoaded', function() {
        
        /**
         * Inisialisasi fungsionalitas untuk tombol Wishlist.
         */
        function initWishlistButton() {
            @auth
            const btn = document.getElementById('btnWishlist');
            if (!btn) return;

            const url = btn.getAttribute('data-toggle-url');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Helper untuk merender tampilan tombol (aktif/tidak aktif)
            const renderButtonState = (isActive) => {
                btn.style.backgroundColor = isActive ? '#dc3545' : '#ffffff';
                btn.style.borderColor = '#dc3545';
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                btn.setAttribute('aria-label', isActive ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist');
                btn.title = isActive ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist';
                btn.innerHTML = `<i class="fa fa-heart" style="font-size:18px; color:${isActive ? '#ffffff' : '#dc3545'}"></i>`;
            };

            btn.addEventListener('click', async function() {
                if (btn.disabled) return;
                
                btn.disabled = true;
                const originalContent = btn.innerHTML; // Simpan konten asli
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>'; // Tampilkan spinner

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: new FormData()
                    });

                    const data = await response.json();

                    if (data?.status === 'added') {
                        renderButtonState(true);
                        showToast(data.message || 'Ditambahkan ke wishlist.');
                    } else if (data?.status === 'removed') {
                        renderButtonState(false);
                        showToast(data.message || 'Dihapus dari wishlist.');
                    } else {
                        // Jika respons bukan JSON yang diharapkan, fallback dengan reload
                        location.reload();
                    }
                } catch (error) {
                    console.error('Wishlist Error:', error);
                    btn.innerHTML = originalContent; // Kembalikan ke state semula jika error
                    showToast('Terjadi kesalahan. Silakan coba lagi.', true);
                } finally {
                    btn.disabled = false;
                }
            });
            @endauth
        }

        /**
         * Inisialisasi fungsionalitas untuk input kuantitas produk.
         */
        function initQuantityInput() {
            const qtyInput = document.getElementById('qtyInput');
            const minusBtn = document.getElementById('btnQtyMinus');
            const plusBtn = document.getElementById('btnQtyPlus');
            const totalPriceEl = document.getElementById('totalPrice');
            const addToCartBtn = document.getElementById('btnAddCart');

            if (!qtyInput || !totalPriceEl) return;

            const unitPrice = parseInt(qtyInput.dataset.price || '0', 10);
            const maxStock = parseInt(qtyInput.dataset.max || qtyInput.getAttribute('max') || '0', 10);
            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });

            const updateUI = () => {
                let currentValue = parseInt(qtyInput.value, 10) || 1;

                // Batasi nilai antara 1 dan stok maksimum
                if (currentValue < 1) currentValue = 1;
                if (maxStock > 0 && currentValue > maxStock) currentValue = maxStock;
                
                qtyInput.value = currentValue;

                // Update status tombol +/-
                if (minusBtn) minusBtn.disabled = (currentValue <= 1);
                if (plusBtn) plusBtn.disabled = (maxStock > 0 && currentValue >= maxStock);

                // Update total harga
                totalPriceEl.textContent = currencyFormatter.format(unitPrice * currentValue);
                
                // Nonaktifkan tombol "Add to Cart" jika stok habis
                if (addToCartBtn) addToCartBtn.disabled = (maxStock <= 0);
            };

            minusBtn?.addEventListener('click', () => {
                qtyInput.value--;
                updateUI();
            });
            plusBtn?.addEventListener('click', () => {
                qtyInput.value++;
                updateUI();
            });

            qtyInput.addEventListener('input', updateUI);
            qtyInput.addEventListener('change', updateUI);

            // Inisialisasi tampilan saat halaman pertama kali dimuat
            updateUI();
        }

        // Jalankan semua fungsi inisialisasi
        initWishlistButton();
        initQuantityInput();

    });
</script>
@endpush

</x-app-layout>