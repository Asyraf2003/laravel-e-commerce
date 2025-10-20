<x-app-layout>
    <x-slot>
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Shop Page</h1>
            <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active text-white">Shop</li>
            </ol>
        </div>
        <!-- Single Page Header End -->

        <!-- Searvices Start -->
        <div class="container-fluid px-0">
            <div class="row g-0">
                <div class="col-6 col-md-4 col-lg-2 border-start border-end wow fadeInUp" data-wow-delay="0.1s">
                    <div class="p-4">
                        <div class="d-inline-flex align-items-center">
                            <i class="fa fa-sync-alt fa-2x text-primary"></i>
                            <div class="ms-4">
                                <h6 class="text-uppercase mb-2">Free Return</h6>
                                <p class="mb-0">30 days money back guarantee!</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.2s">
                    <div class="p-4">
                        <div class="d-flex align-items-center">
                            <i class="fab fa-telegram-plane fa-2x text-primary"></i>
                            <div class="ms-4">
                                <h6 class="text-uppercase mb-2">Free Shipping</h6>
                                <p class="mb-0">Free shipping on all order</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.3s">
                    <div class="p-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-life-ring fa-2x text-primary"></i>
                            <div class="ms-4">
                                <h6 class="text-uppercase mb-2">Support 24/7</h6>
                                <p class="mb-0">We support online 24 hrs a day</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.4s">
                    <div class="p-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-credit-card fa-2x text-primary"></i>
                            <div class="ms-4">
                                <h6 class="text-uppercase mb-2">Receive Gift Card</h6>
                                <p class="mb-0">Recieve gift all over oder $50</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.5s">
                    <div class="p-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lock fa-2x text-primary"></i>
                            <div class="ms-4">
                                <h6 class="text-uppercase mb-2">Secure Payment</h6>
                                <p class="mb-0">We Value Your Security</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.6s">
                    <div class="p-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-blog fa-2x text-primary"></i>
                            <div class="ms-4">
                                <h6 class="text-uppercase mb-2">Online Service</h6>
                                <p class="mb-0">Free return products in 30 days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Searvices End -->

        <!-- Shop Page Start -->
        <div class="container-fluid shop py-5">
            <div class="container py-5">
                <div class="row g-4">
                {{-- Sidebar kategori --}}
                <div class="col-lg-3">
                    <div class="product-categories mb-4">
                    <h4>Products Categories</h4>
                    <ul class="list-unstyled" id="category-list">
                        {{-- All --}}
                        <li>
                        <div class="categories-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('shop.index') }}"
                            class="text-dark {{ !$activeCategory ? 'fw-bold' : '' }}"
                            data-category="">
                            <i class="fas fa-apple-alt text-secondary me-2"></i> All
                            </a>
                            <span>({{ $categories->sum('products_count') }})</span>
                        </div>
                        </li>
                        @foreach($categories as $cat)
                        @php $isActive = $activeCategory && $activeCategory->id === $cat->id; @endphp
                        <li>
                            <div class="categories-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('shop.index', ['category' => $cat->slug, 'q' => request('q')]) }}"
                                class="text-dark {{ $isActive ? 'fw-bold' : '' }}"
                                data-category="{{ $cat->slug }}">
                                <i class="fas fa-apple-alt text-secondary me-2"></i>{{ $cat->name }}
                            </a>
                            <span>({{ $cat->products_count }})</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    </div>
                </div>

                {{-- Main --}}
                <div class="col-lg-9">
                    <div class="row g-4 align-items-center mb-3">
                        <div class="col-xl-7">
                            {{-- Formulir pencarian, mengarah ke route 'shop.index' dengan method GET --}}
                            <form action="{{ route('shop.index') }}" method="GET" class="w-100">
                                <div class="d-flex align-items-center gap-2">

                                    {{-- Bagian Input Teks --}}
                                    <div class="input-group input-group-lg rounded-pill shadow-sm overflow-hidden flex-grow-1">
                                        <input type="search" 
                                            id="search-input" 
                                            name="q" {{-- Atribut 'name' ini penting agar request('q') berfungsi --}}
                                            class="form-control border-0 shadow-none p-3" 
                                            placeholder="Search products..." 
                                            value="{{ request('q') ?? '' }}"
                                            aria-label="Search products">
                                    </div>

                                    {{-- Tombol Submit Pencarian --}}
                                    <button class="btn btn-primary btn-lg rounded-pill shadow-sm px-4" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- WRAP: ini yang akan diganti via AJAX --}}
                    <div class="tab-content">
                        <div class="p-0">
                            {{-- WRAP GRID --}}
                            <div id="products-wrap" aria-live="polite">
                            @include('partials._products-grid', ['products' => $products])
                            </div>

                            <div id="pagination-wrap" class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </x-slot>
    @push('scripts')
    <script>
    (function() {
    const productsWrap   = document.getElementById('products-wrap');
    const paginationWrap = document.getElementById('pagination-wrap');
    const categoryList   = document.getElementById('category-list');
    const searchInput    = document.getElementById('search-input');

    const params = new URLSearchParams(location.search);
    const state = {
        category: params.get('category') || '',
        q:        params.get('q') || '',
        page:     parseInt(params.get('page') || '1', 10),
    };

    function buildUrl() {
        const usp = new URLSearchParams();
        if (state.category) usp.set('category', state.category);
        if (state.q)        usp.set('q', state.q);
        if (state.page > 1) usp.set('page', state.page);
        return "{{ route('shop.index') }}" + (usp.toString() ? ('?' + usp.toString()) : '');
    }

    let inflight = 0;
    let debounceTimer = null;

    async function fetchProducts(pushHistory = true) {
        inflight++;
        productsWrap.classList.add('is-loading'); // fade halus, bukan spinner
        try {
        const url = buildUrl();
        const res = await fetch(url, {
            headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
            }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        productsWrap.innerHTML = data.grid;
        paginationWrap.innerHTML = data.pagination;

        if (pushHistory) history.pushState({...state}, '', url);
        updateActiveCategory();
        } catch (err) {
        // Fallback jika AJAX gagal: full load
        window.location.href = buildUrl();
        } finally {
        inflight = Math.max(0, inflight - 1);
        if (inflight === 0) productsWrap.classList.remove('is-loading');
        }
    }

    function updateActiveCategory() {
        if (!categoryList) return;
        categoryList.querySelectorAll('a[data-category]').forEach(a => {
        const slug = a.getAttribute('data-category') ?? '';
        a.classList.toggle('fw-bold', slug === state.category);
        });
    }

    // Klik kategori (tanpa reload)
    if (categoryList) {
        categoryList.addEventListener('click', function(e) {
        const a = e.target.closest('a[data-category]');
        if (!a) return;
        e.preventDefault();
        state.category = a.getAttribute('data-category') || '';
        state.page = 1;
        fetchProducts(true);
        });
    }

    // Klik pagination (delegation)
    if (paginationWrap) {
        paginationWrap.addEventListener('click', function(e) {
        const a = e.target.closest('a');
        if (!a || !a.href) return;
        // biarkan tombol disabled / active (tanpa href) lewat
        const href = a.getAttribute('href');
        if (!href || href === '#' || href.startsWith('javascript:')) return;
        e.preventDefault();
        const url = new URL(a.href, window.location.origin);
        state.page = parseInt(url.searchParams.get('page') || '1', 10);
        fetchProducts(true);
        });
    }

    // Live search (on input, debounce 300ms)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
        state.q = this.value.trim();
        state.page = 1;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchProducts(true), 300);
        });
    }

    // Back/forward button
    window.addEventListener('popstate', function() {
        const p = new URLSearchParams(location.search);
        state.category = p.get('category') || '';
        state.q        = p.get('q') || '';
        state.page     = parseInt(p.get('page') || '1', 10);
        fetchProducts(false);
    });

    // Set initial active category UI (optional)
    updateActiveCategory();
    })();
    </script>

    <style>
    /* Efek halus ketika konten diganti: tanpa spinner, no "refreshy" feel */
    #products-wrap { transition: opacity .2s ease; }
    #products-wrap.is-loading { opacity: .5; }
    </style>
    @endpush
</x-app-layout>