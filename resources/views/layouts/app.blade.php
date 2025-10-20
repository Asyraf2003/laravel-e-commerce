<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Topbar Start -->
    <div class="container-fluid px-5 d-none border-bottom d-lg-block">
        <div class="row gx-0 align-items-center">
            <!-- Left Menu -->
            <div class="col-lg-4 text-center text-lg-start mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a href="#" class="text-muted me-2">Help</a><small> / </small>
                    <a href="#" class="text-muted mx-2">Support</a><small> / </small>
                    <a href="#" class="text-muted ms-2">Contact</a>
                </div>
            </div>
            <div class="col-lg-4"></div>
            <!-- Right Menu -->
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <!-- Currency Switch -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted me-2" data-bs-toggle="dropdown">
                            <small>IDR</small>
                        </a>
                        <div class="dropdown-menu rounded">
                            <a href="#" class="dropdown-item text-muted">USD (Belum Tersedia)</a>
                            <a href="#" class="dropdown-item text-muted">EUR (Belum Tersedia)</a>
                        </div>
                    </div>

                    <!-- Language Switch -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted mx-2" data-bs-toggle="dropdown">
                            <small>English</small>
                        </a>
                        <div class="dropdown-menu rounded">
                            <a href="#" class="dropdown-item text-muted">Indonesia (Belum Tersedia)</a>
                            <a href="#" class="dropdown-item text-muted">Arabic (Belum Tersedia)</a>
                            <a href="#" class="dropdown-item text-muted">Spanish (Belum Tersedia)</a>
                            <a href="#" class="dropdown-item text-muted">Italian (Belum Tersedia)</a>
                        </div>
                    </div>

                    <!-- Account -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted ms-2" data-bs-toggle="dropdown">
                            <small><i class="fa fa-user me-2"></i> Account</small>
                        </a>
                        <div class="dropdown-menu rounded">
                            @guest
                                <a href="{{ route('login') }}" class="dropdown-item">Login</a>
                                <a href="{{ route('register') }}" class="dropdown-item">Register</a>
                            @else
                                @can('viewAny', App\Models\User::class)
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">Admin Dashboard</a>
                                @endcan

                                <a href="{{ route('profile.edit') }}" class="dropdown-item">Account Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid px-5 py-4 d-none d-lg-block">
        {{-- 1. Gunakan justify-content-between untuk mendorong item ke kiri dan kanan --}}
        <div class="row gx-0 align-items-center justify-content-between">

            {{-- 2. Kolom kiri untuk logo (gunakan col-auto agar lebarnya pas) --}}
            <div class="col-auto">
                <a href="/" class="navbar-brand p-0">
                    <h1 class="display-5 text-primary m-0">AsyrafShop</h1>
                </a>
            </div>

            {{-- 3. Kolom kanan untuk grup ikon --}}
            <div class="col-auto">
                {{-- Grupkan ikon dengan d-flex agar sejajar rapi --}}
                <div class="d-flex align-items-center">

                    {{-- Wishlist --}}
                    <a href="{{ route('user.wishlist.index') }}" class="position-relative text-muted me-3">
                        <span class="rounded-circle border d-inline-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                            <i class="fas fa-heart"></i>
                        </span>
                        {{-- ID diubah agar sesuai dengan Javascript update badge --}}
                        <span id="wishlist-badge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($wishlistCount ?? 0) ? '' : 'd-none' }}">
                            {{ $wishlistCount ?? 0 }}
                        </span>
                    </a>

                    {{-- Cart --}}
                    <a href="{{ route('user.cart.index') }}" class="position-relative text-muted">
                        <span class="rounded-circle border d-inline-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        {{-- ID diubah agar sesuai dengan Javascript update badge --}}
                        <span id="cart-badge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary {{ ($cartCount ?? 0) ? '' : 'd-none' }}">
                            {{ $cartCount ?? 0 }}
                        </span>
                    </a>

                </div>
            </div>

        </div>
    </div>

    <!-- Navbar & Hero Start -->
    <div class="container-fluid nav-bar p-0">
        <div class="row gx-0 bg-primary px-5 align-items-center">
            <div class="col-lg-3 d-none d-lg-block">
            </div>
            <div class="col-12 col-lg-9">
                <nav class="navbar navbar-expand-lg navbar-light bg-primary ">
                    <a href="" class="navbar-brand d-block d-lg-none">
                        <h1 class="display-5 text-secondary m-0">AsyrafShop</h1>
                    </a>
                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars fa-1x"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav ms-auto py-0">
                            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                            <a href="{{ route('shop.index') }}" class="nav-item nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}">Shop</a>
                            <a href="{{ route('user.wishlist.index') }}" class="nav-item nav-link me-2 {{ request()->routeIs('user.wishlist.index') ? 'active' : '' }}">Wishlist</a>
                            <a href="{{ route('user.cart.index') }}" class="nav-item nav-link me-2 {{ request()->routeIs('user.cart.index') ? 'active' : '' }}">Cart</a>
                            <a href="{{ route('user.history.index') }}" class="nav-item nav-link me-2 {{ request()->routeIs('user.history.index') ? 'active' : '' }}">History</a>
                            <a href="{{ route('contact') }}" class="nav-item nav-link me-2 {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    
    <main>
        {{ $slot }}
    </main>
    
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <div class="footer-item">
                            <h4 class="text-primary mb-4">Newsletter</h4>
                            <p class="mb-3">Dolor amet sit justo amet elitr clita ipsum elitr est.Lorem ipsum dolor sit
                                amet, consectetur adipiscing elit consectetur adipiscing elit.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-primary mb-4">Customer Service</h4>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Contact Us</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Returns</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Order History</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Site Map</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Testimonials</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> My Account</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Unsubscribe Notification</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-primary mb-4">Information</h4>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> About Us</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Delivery infomation</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Privacy Policy</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Terms & Conditions</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Warranty</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> FAQ</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Seller Login</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="text-primary mb-4">Extras</h4>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Brands</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Gift Vouchers</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Affiliates</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Wishlist</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Order History</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Track Your Order</a>
                        <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Track Your Order</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-white"><a href="#" class="border-bottom text-white"><i
                                class="fas fa-copyright text-light me-2"></i>AsyrafShop</a>, All right
                        reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-white">

                    <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                    <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                    <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                    Designed By <a class="border-bottom text-white" href="https://htmlcodex.com">HTML Codex</a>.
                    Distributed By <a class="border-bottom text-white" href="https://themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-primary btn-lg-square back-to-top">
        <i class="fa fa-arrow-up"></i>
    </a>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
    // Set angka badge sesuai nilai server (sinkron)
    window.updateBadge = function(type, count) {
    const n = Math.max(0, parseInt(count ?? 0, 10) || 0);
    const selectors = type === 'wishlist'
        ? ['#badgeWishlistCount', '.js-badge-wishlist']
        : ['#badgeCartCount', '.js-badge-cart'];

    document.querySelectorAll(selectors.join(',')).forEach(el => {
        el.textContent = n;
        el.classList.toggle('d-none', n === 0);
    });
    };

    // Fallback kalau server belum kirim count (hindari jika bisa)
    window.bumpBadge = function(type, delta) {
    const id = type === 'wishlist' ? '#badgeWishlistCount' : '#badgeCartCount';
    const el = document.querySelector(id);
    const curr = Math.max(0, parseInt(el?.textContent || '0', 10) || 0);
    window.updateBadge(type, curr + (parseInt(delta, 10) || 0));
    };
    const res  = await fetch(url, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
    body: new FormData()
    });
    const data = await res.json().catch(()=>null);

    if (data?.status === 'added') {
    render(true);
    if (typeof data.wishlist_count !== 'undefined') {
        window.updateBadge('wishlist', data.wishlist_count);
    }
    showToast(data.message || 'Ditambahkan ke wishlist.');
    } else if (data?.status === 'removed') {
    render(false);
    if (typeof data.wishlist_count !== 'undefined') {
        window.updateBadge('wishlist', data.wishlist_count);
    }
    showToast(data.message || 'Dihapus dari wishlist.');
    } else {
    location.reload();
    }
    </script>
    @stack('scripts')
</body>
</html>
