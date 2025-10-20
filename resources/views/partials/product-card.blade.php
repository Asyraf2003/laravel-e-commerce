<div class="col-md-6 col-lg-4 col-xl-3">
  @php
    $discPercent       = (int) ($product->discount_percent ?? 0);
    $hasDiscount       = $discPercent > 0;
    $finalPrice        = (int) ($product->final_price ?? 0);
    $finalPriceText    = 'Rp' . number_format($finalPrice, 0, ',', '.');
    $originalPrice     = (int) ($product->original_price ?? 0);
    $originalPriceText = 'Rp' . number_format($originalPrice, 0, ',', '.');
    $stock             = (int) ($product->stock ?? 0);
    $imgPath           = $product->primaryImage->image_path ?? 'assets/img/placeholder.png';

    // fallback nama route: user.cart.add / cart.add dan user.cart.index / cart.index
    $addRoute = \Illuminate\Support\Facades\Route::has('user.cart.add')
      ? route('user.cart.add', $product)
      : route('cart.add', $product);

    $cartIndexRoute = \Illuminate\Support\Facades\Route::has('user.cart.index')
      ? route('user.cart.index')
      : route('cart.index');

    $avg = (int) round($product->reviews_avg ?? 0);

    $isWished = auth()->check()
        ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
        : false;

    $bg   = $isWished ? '#dc3545' : '#ffffff';
    $icon = $isWished ? '#ffffff' : '#dc3545';

    // URL toggle & count (pakai prefix 'user.' sesuai permintaan)
    $wishlistToggleUrl = route('user.wishlist.toggle', $product);
    $wishlistCountUrl  = route('user.wishlist.count');

    // URL detail produk untuk share
    $productUrl = route('shop.detail', $product->id);
  @endphp

  <div class="product-item rounded">
    <div class="product-item-inner border rounded">
      <div class="product-item-inner-item">
        <img src="{{ asset($imgPath) }}" class="img-fluid w-100 rounded-top" alt="{{ $product->name }}">

        @if($product->is_new)
          <div class="product-new">New</div>
        @elseif($product->is_hot)
          <div class="product-new bg-danger">Hot</div>
        @endif

        <div class="product-details">
          <a href="{{ route('shop.detail', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
        </div>
      </div>

      <div class="text-center rounded-bottom p-4">
        <a href="#" class="d-block mb-2">{{ $product->category->name ?? 'Uncategorized' }}</a>
        <a href="#" class="d-block h4">{{ $product->name }}</a>

        @if($hasDiscount)
          <del class="me-2 fs-5 text-muted">{{ $originalPriceText }}</del>
          <span class="text-primary fs-5 fw-semibold">{{ $finalPriceText }}</span>
          <span class="badge bg-danger ms-2">{{ $discPercent }}% OFF</span>
        @else
          <span class="text-primary fs-5 fw-semibold">{{ $finalPriceText }}</span>
        @endif
      </div>
    </div>

    <div class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
      @auth
        @php $soldOut = $stock <= 0; @endphp
        <button type="button"
                class="btn btn-primary border-secondary rounded-pill py-2 px-4 mb-4 btn-add-cart"
                data-post-url="{{ $addRoute }}"
                data-cart-index-url="{{ $cartIndexRoute }}"
                data-product-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-image="{{ asset($imgPath) }}"
                data-category="{{ $product->category->name ?? 'Uncategorized' }}"
                data-original-text="{{ $originalPriceText }}"
                data-discount-percent="{{ $discPercent }}"
                data-final-text="{{ $finalPriceText }}"
                data-stock="{{ $stock }}"
                {{ $soldOut ? 'disabled' : '' }}>
          <i class="fas fa-shopping-cart me-2"></i> {{ $soldOut ? 'Stok Habis' : 'Add To Cart' }}
        </button>
      @else
        <a href="{{ route('login') }}" class="btn btn-primary border-secondary rounded-pill py-2 px-4 mb-4">
          <i class="fas fa-shopping-cart me-2"></i> Add To Cart
        </a>
      @endauth

      <div class="d-flex justify-content-between align-items-center mt-2">
        {{-- KIRI: RATING --}}
        <div>
          @php
            $avg   = (float) ($product->approved_reviews_avg ?? 0);
            $count = (int)   ($product->approved_reviews_count ?? 0);
            $avg   = max(0, min(5, $avg));
            $full  = floor($avg);
            $frac  = $avg - $full;
            $half  = $frac >= 0.75 ? 0 : ($frac >= 0.25 ? 1 : 0);
            if ($frac >= 0.75) $full++;
            $empty = max(0, 5 - $full - $half);
          @endphp

          <div class="d-flex align-items-center">
            @for($i=0;$i<$full;$i++)  <i class="fas fa-star text-warning"></i> @endfor
            @if($half)                <i class="fas fa-star-half-alt text-warning"></i> @endif
            @for($i=0;$i<$empty;$i++) <i class="far fa-star text-warning"></i> @endfor
          </div>
        </div>

        {{-- KANAN: SHARE + WISHLIST --}}
        <div class="d-flex align-items-center">

          {{-- SHARE --}}
          <div class="dropdown me-2">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-share-alt me-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item share-link" href="#"
                  data-title="{{ $product->name }}"
                  data-url="{{ $productUrl }}"
                  data-network="whatsapp">
                  <i class="fab fa-whatsapp me-2"></i> WhatsApp
                </a>
              </li>
              <li>
                <a class="dropdown-item share-link" href="#"
                  data-title="{{ $product->name }}"
                  data-url="{{ $productUrl }}"
                  data-network="x">
                  <i class="fab fa-twitter me-2"></i> Twitter
                </a>
              </li>
              <li>
                <a class="dropdown-item share-link" href="#"
                  data-title="{{ $product->name }}"
                  data-url="{{ $productUrl }}"
                  data-network="facebook">
                  <i class="fab fa-facebook me-2"></i> Facebook
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item share-link" href="#"
                  data-title="{{ $product->name }}"
                  data-url="{{ $productUrl }}"
                  data-network="copy">
                  <i class="fas fa-link me-2"></i> Copy Link
                </a>
              </li>
            </ul>
          </div>

          {{-- WISHLIST --}}
          @auth
            <button type="button"
              class="btn-wishlist text-primary d-flex align-items-center justify-content-center"
              data-toggle-url="{{ $wishlistToggleUrl }}"
              data-count-url="{{ $wishlistCountUrl }}"
              data-active="{{ $isWished ? '1' : '0' }}"
              aria-pressed="{{ $isWished ? 'true' : 'false' }}"
              aria-label="{{ $isWished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
              title="{{ $isWished ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
              style="border:none;background:transparent;">
              <span class="rounded-circle btn-sm-square border"
                style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-color:#dc3545;background-color: {{ $bg }}; transition:all .2s ease;">
                <i class="fas fa-heart" style="color: {{ $icon }};"></i>
              </span>
            </button>
          @else
            <a href="{{ route('login') }}" class="text-primary d-flex align-items-center justify-content-center" title="Login untuk wishlist">
              <span class="rounded-circle btn-sm-square border" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;"><i class="fas fa-heart"></i></span>
            </a>
          @endauth
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== Modal Qty: render SEKALI SAJA di halaman (hindari duplikat id) ===== --}}
@auth
  @once
    <div class="modal fade" id="cartQtyModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form id="cartQtyForm" class="modal-content">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Tambah ke Keranjang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>

          <div class="modal-body">
            <div class="d-flex gap-3">
              <img id="cartProdImage" src="" alt="Product" style="width:84px;height:84px;object-fit:cover;border-radius:8px;">
              <div class="flex-grow-1">
                <div class="small text-muted" id="cartProdCategory">Kategori</div>
                <div class="fw-semibold" id="cartProdName">Nama Produk</div>
                <div class="mt-1">
                  <span class="text-muted me-2 small" id="cartProdOriginal" style="text-decoration: line-through; display:none;"></span>
                  <span class="fw-semibold text-primary" id="cartProdFinal">Rp0</span>
                  <span class="badge bg-danger ms-2" id="cartProdDisc" style="display:none;">-0%</span>
                </div>
                <div class="small text-muted mt-1">
                  Stok tersedia: <span id="cartProdStock">0</span>
                  <span class="ms-2 d-none" id="cartInCartWrap">
                    • Di keranjang: <span id="cartInCartQty">0</span>
                  </span>
                </div>
              </div>
            </div>

            <hr class="my-3">

            <div class="d-flex align-items-center justify-content-between">
              <label for="qtyInput" class="me-3 mb-0">Jumlah</label>
              <div class="input-group" style="width: 200px;">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="qtyMinus">
                  <i class="fa fa-minus"></i>
                </button>
                <input id="qtyInput" name="qty" type="number"
                       class="form-control form-control-sm text-center"
                       value="1" min="1" step="1" inputmode="numeric" pattern="[0-9]*">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="qtyPlus">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
              <div class="small text-muted" id="qtyMaxNote" style="min-width:120px;text-align:right;"></div>
            </div>

            <div class="alert alert-danger d-none mt-3 mb-0" id="cartQtyError"></div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="cartSubmitBtn">
              <i class="fa fa-shopping-bag me-2"></i> Tambahkan
            </button>
          </div>
        </form>
      </div>
    </div>
  @endonce
@endauth

@once
@push('scripts')
@auth
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Cek Bootstrap
  const hasBootstrap = !!(window.bootstrap && bootstrap.Modal);

  // Elemen yang digunakan (boleh belum ada saat ini)
  const form        = document.getElementById('cartQtyForm');
  const elName      = document.getElementById('cartProdName');
  const elCat       = document.getElementById('cartProdCategory');
  const elImg       = document.getElementById('cartProdImage');
  const elOrig      = document.getElementById('cartProdOriginal');
  const elFinal     = document.getElementById('cartProdFinal');
  const elDisc      = document.getElementById('cartProdDisc');
  const elStock     = document.getElementById('cartProdStock');
  const inCartWrap  = document.getElementById('cartInCartWrap');
  const inCartQty   = document.getElementById('cartInCartQty');
  const qtyInput    = document.getElementById('qtyInput');
  const btnMinus    = document.getElementById('qtyMinus');
  const btnPlus     = document.getElementById('qtyPlus');
  const maxNote     = document.getElementById('qtyMaxNote');
  const errBox      = document.getElementById('cartQtyError');
  const submitBtn   = document.getElementById('cartSubmitBtn');

  let postUrl = null, cartIndexUrl = null, productId = null, stock = 0, maxAdd = 0;

  function getModal() {
    if (!hasBootstrap) return null;
    const el = document.getElementById('cartQtyModal');
    return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
  }

  function showError(msg) {
    if (!errBox) return;
    errBox.textContent = msg || 'Terjadi kesalahan.';
    errBox.classList.remove('d-none');
  }
  function clearError() {
    if (!errBox) return;
    errBox.textContent = '';
    errBox.classList.add('d-none');
  }
  function clampQty() {
    if (!qtyInput) return;
    let v = parseInt(qtyInput.value || '1', 10);
    if (isNaN(v) || v < 1) v = 1;
    if (maxAdd > 0 && v > maxAdd) v = maxAdd;
    qtyInput.value = v;
  }
  function updateMaxNote() {
    if (!maxNote || !submitBtn) return;
    maxNote.textContent = maxAdd > 0 ? ('Maks: ' + maxAdd) : '';
    submitBtn.disabled = (maxAdd < 1);
  }

  // Delegasi klik untuk semua tombol .btn-add-cart (banner & card)
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.btn-add-cart');
    if (!btn) return;

    const modal = getModal();
    if (!modal) {
      console.warn('Bootstrap/Modal belum tersedia atau elemen #cartQtyModal tidak ditemukan.');
      return;
    }

    // Data dari tombol
    postUrl      = btn.dataset.postUrl || null;
    cartIndexUrl = btn.dataset.cartIndexUrl || null;
    productId    = parseInt(btn.dataset.productId || '0', 10);
    stock        = parseInt(btn.dataset.stock || '0', 10);

    if (!postUrl || !cartIndexUrl || !productId) return;

    // Isi konten modal
    if (elName) elName.textContent = btn.dataset.name || 'Produk';
    if (elCat)  elCat.textContent  = btn.dataset.category || 'Uncategorized';
    if (elImg)  elImg.src          = btn.dataset.image || '';
    if (elFinal) elFinal.textContent = btn.dataset.finalText || 'Rp0';

    const originalText = btn.dataset.originalText || '';
    const discPercent  = parseInt(btn.dataset.discountPercent || '0', 10);
    if (discPercent > 0 && originalText) {
      if (elOrig) { elOrig.style.display = ''; elOrig.textContent = originalText; }
      if (elDisc) { elDisc.style.display = ''; elDisc.textContent = '-' + discPercent + '%'; }
    } else {
      if (elOrig) elOrig.style.display = 'none';
      if (elDisc) elDisc.style.display = 'none';
    }

    if (elStock) elStock.textContent = stock;

    // Reset form
    if (qtyInput) qtyInput.value = 1;
    clearError();
    if (inCartWrap) inCartWrap.classList.add('d-none');
    if (inCartQty)  inCartQty.textContent = '0';

    // Hitung qty sudah di cart (produk ini) + total cart
    let already = 0;
    try {
      const res = await fetch(cartIndexUrl, { headers: { 'Accept': 'application/json' } });
      if (res.ok) {
        const data = await res.json();

        // 1) qty produk ini
        const items = Array.isArray(data?.items) ? data.items : [];
        const found = items.find(it => parseInt(it.product_id, 10) === productId);
        if (found) {
          already = parseInt(found.qty || '0', 10) || 0;
          if (inCartQty)  inCartQty.textContent = already;
          if (inCartWrap) inCartWrap.classList.remove('d-none');
        }

        // 2) total di keranjang
        const totalQty = parseInt(data?.summary?.total_qty ?? '0', 10) || 0;
        const totalWrap = document.getElementById('cartTotalWrap');
        const totalEl   = document.getElementById('cartTotalQty');
        if (totalWrap && totalEl) {
          totalEl.textContent = totalQty;
          totalWrap.classList.remove('d-none');
        }
      }
    } catch (_) {}

    maxAdd = Math.max(0, stock - already);
    clampQty();
    updateMaxNote();

    modal.show();
  });

  // Qty +/- handler
  btnMinus?.addEventListener('click', () => {
    const v = parseInt(qtyInput.value || '1', 10) || 1;
    qtyInput.value = Math.max(1, v - 1);
  });
  btnPlus?.addEventListener('click', () => {
    const v = parseInt(qtyInput.value || '1', 10) || 1;
    qtyInput.value = maxAdd > 0 ? Math.min(maxAdd, v + 1) : (v + 1);
  });
  qtyInput?.addEventListener('input', clampQty);

  // Submit → POST ke add
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearError();
    if (!postUrl) return showError('URL tidak valid.');

    let qty = parseInt(qtyInput.value || '1', 10);
    if (isNaN(qty) || qty < 1) qty = 1;
    if (maxAdd > 0 && qty > maxAdd) qty = maxAdd;

    const fd = new FormData(form);
    fd.set('qty', String(qty));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    submitBtn && (submitBtn.disabled = true);
    try {
      const res = await fetch(postUrl, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: fd
      });

      if (res.status === 422) {
        const j = await res.json().catch(() => ({}));
        return showError(j?.message || 'Qty tidak valid.');
      }
      if (!res.ok) return showError('Gagal menambahkan ke keranjang.');

      getModal()?.hide();
      // TODO: update badge cart/mini cart jika ada
    } catch {
      showError('Koneksi gagal. Coba lagi.');
    } finally {
      submitBtn && (submitBtn.disabled = false);
    }
  });
});
</script>
@endauth
<script>
// ================= SHARE =================
document.addEventListener('click', async (e) => {
  const a = e.target.closest('.share-link');
  if (!a) return;

  e.preventDefault();

  const title = a.getAttribute('data-title') || document.title;
  const url   = a.getAttribute('data-url') || location.href;
  const net   = a.getAttribute('data-network') || 'native';

  if (net === 'native' && navigator.share) {
    try { await navigator.share({ title, url }); } catch(_) {}
    return;
  }

  let shareUrl = url;
  if (net === 'whatsapp') {
    shareUrl = 'https://wa.me/?text=' + encodeURIComponent(`${title} ${url}`);
  } else if (net === 'x') {
    shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url);
  } else if (net === 'facebook') {
    shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
  } else if (net === 'copy') {
    try {
      await navigator.clipboard.writeText(url);
      alert('Link disalin!');
    } catch (_) {
      const t = document.createElement('textarea');
      t.value = url; document.body.appendChild(t); t.select();
      try { document.execCommand('copy'); alert('Link disalin!'); } catch(_) { alert(url); }
      document.body.removeChild(t);
    }
    return;
  }
  window.open(shareUrl, '_blank', 'noopener,noreferrer,width=720,height=560');
});

// ================= WISHLIST =================
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.btn-wishlist');
  if (!btn || btn.disabled) return;

  btn.disabled = true;

  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
  const url   = btn.getAttribute('data-toggle-url');
  const countUrl = btn.getAttribute('data-count-url') || '';
  const wrap  = btn.querySelector('.rounded-circle');
  const icon  = btn.querySelector('i');

  if (!url || url === '#') { btn.disabled = false; return; }

  const oldIconClass = icon?.className || '';
  const oldColor     = icon?.style?.color || '';

  if (icon) {
    icon.className = 'fas fa-spinner fa-spin';
    icon.style.color = '#6c757d';
  }

  const render = (active) => {
    if (wrap) {
      wrap.style.backgroundColor = active ? '#dc3545' : '#ffffff';
      wrap.style.borderColor     = '#dc3545';
    }
    if (icon) {
      icon.className = 'fas fa-heart';
      icon.style.color = active ? '#ffffff' : '#dc3545';
    }
    btn.setAttribute('data-active', active ? '1' : '0');
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    btn.setAttribute('aria-label', active ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist');
    btn.title = active ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist';
  };

  const badge = document.querySelector('[data-wishlist-badge]');
  async function refreshCount() {
    if (!badge || !countUrl) return;
    try {
      const r = await fetch(countUrl, { headers: { 'Accept': 'application/json' } });
      if (r.ok) {
        const j = await r.json().catch(() => ({}));
        if (typeof j.total === 'number') badge.textContent = j.total;
      }
    } catch (_) {}
  }

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      body: new FormData()
    });

    if (res.status === 401) return window.location.href = "{{ route('login') }}";
    if (res.status === 419) { alert('Sesi kedaluwarsa. Silakan refresh & login ulang.'); return window.location.reload(); }
    if (res.status === 403) { alert('Akses ditolak.'); return; }
    if (!res.ok) { alert('Gagal memproses wishlist.'); return; }

    const data = await res.json().catch(() => ({}));
    if (data?.status === 'added')  { render(true);  await refreshCount(); }
    else if (data?.status === 'removed') { render(false); await refreshCount(); }
    else { window.location.reload(); }

  } catch (err) {
    if (icon) { icon.className = oldIconClass; icon.style.color = oldColor; }
    alert('Gagal memproses wishlist. Coba lagi ya.');
  } finally {
    btn.disabled = false;
  }
});
</script>
@endpush
@endonce


