<x-app-layout>
    <x-slot>
        <!-- Carousel Start -->
        <div class="container-fluid carousel bg-light px-0">
            <div class="row g-0 justify-content-end">
                <div class="col-12 col-lg-7 col-xl-9">
                    <div class="header-carousel owl-carousel bg-light py-5">
                        <!-- Item 1 -->
                        <div class="row g-0 header-carousel-item align-items-center">
                            <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                                <img src="{{ asset('assets/img/carousel-1.png') }}" class="img-fluid w-100" alt="Image">
                            </div>
                            <div class="col-xl-6 carousel-content p-4">
                                <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s" style="letter-spacing: 3px;">
                                    Save Up To A $400
                                </h4>
                                <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">
                                    On Selected Laptops & Desktop Or Smartphone
                                </h1>
                                <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">Terms and Condition Apply</p>
                                <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="{{ route('shop.index') }}">
                                    Shop Now
                                </a>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="row g-0 header-carousel-item align-items-center">
                            <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                                <img src="{{ asset('assets/img/carousel-2.png') }}" class="img-fluid w-100" alt="Image">
                            </div>
                            <div class="col-xl-6 carousel-content p-4">
                                <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s" style="letter-spacing: 3px;">
                                    Save Up To A $200
                                </h4>
                                <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">
                                    On Selected Laptops & Desktop Or Smartphone
                                </h1>
                                <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">Terms and Condition Apply</p>
                                <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s" href="{{ route('shop.index') }}">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Banner -->
                <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
                    <div class="carousel-header-banner h-100">
                        <img src="{{ asset('assets/img/header-img.jpg') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Image">

                        <div class="carousel-banner-offer">
                            <p class="bg-primary text-white rounded fs-5 py-2 px-4 mb-0 me-3">Free Tumbler</p>
                            <p class="text-primary fs-5 fw-bold mb-0">Special Promo</p>
                        </div>

                        @php
                            // Hitung harga & stok untuk bannerProduct
                            $discPercent       = (int) ($bannerProduct->discount_percent ?? 0);
                            $hasDiscount       = $discPercent > 0;
                            $finalPrice        = (int) ($bannerProduct->final_price ?? 0);
                            $finalPriceText    = 'Rp' . number_format($finalPrice, 0, ',', '.');
                            $originalPrice     = (int) ($bannerProduct->original_price ?? 0);
                            $originalPriceText = 'Rp' . number_format($originalPrice, 0, ',', '.');
                            $stock             = (int) ($bannerProduct->stock ?? 0);

                            // Tentukan route add & index dengan fallback (user.cart.* atau cart.*)
                            $addRoute = \Illuminate\Support\Facades\Route::has('user.cart.add')
                                ? route('user.cart.add', $bannerProduct)
                                : route('cart.add', $bannerProduct);

                            $cartIndexRoute = \Illuminate\Support\Facades\Route::has('user.cart.index')
                                ? route('user.cart.index')
                                : route('cart.index');

                            $imgPath = $bannerProduct->primaryImage->image_path ?? 'assets/img/placeholder.png';
                        @endphp


                        <div class="carousel-banner">
                        <div class="carousel-banner-content text-center p-4">
                            <a href="#" class="d-block mb-2">
                            {{ $bannerProduct->category->name ?? 'Uncategorized' }}
                            </a>
                            <a href="#" class="d-block text-white fs-3">
                            {{ $bannerProduct->name }}
                            </a>

                            @if($hasDiscount)
                            <del class="me-2 text-white fs-5">{{ $originalPriceText }}</del>
                            @endif
                            <span class="text-primary fs-5">{{ $finalPriceText }}</span>
                        </div>

                        @auth
                            @php $soldOut = $stock <= 0; @endphp
                            <button type="button"
                                    class="btn btn-primary border-secondary rounded-pill py-2 px-4 mb-4 btn-add-cart"
                                    data-post-url="{{ $addRoute }}"
                                    data-cart-index-url="{{ $cartIndexRoute }}"
                                    data-product-id="{{ $bannerProduct->id }}"
                                    data-name="{{ $bannerProduct->name }}"
                                    data-image="{{ asset($imgPath) }}"
                                    data-category="{{ $bannerProduct->category->name ?? 'Uncategorized' }}"
                                    data-original="{{ $originalPrice }}"
                                    data-original-text="{{ $originalPriceText }}"
                                    data-discount-percent="{{ $discPercent }}"
                                    data-final="{{ $finalPrice }}"
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->

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

        <!-- Products Offer Start -->
        <div class="container-fluid bg-light py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.2s">
                        <a href="{{ route('shop.index') }}" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                            <div>
                                <p class="text-muted mb-3">Find The Best Camera for You!</p>
                                <h3 class="text-primary">Smart Camera</h3>
                                <h1 class="display-3 text-secondary mb-0">40% 
                                    <span class="text-primary fw-normal">Off</span>
                                </h1>
                            </div>
                            <img src="{{ asset('assets/img/product-1.png') }}" class="img-fluid" alt="Smart Camera">
                        </a>
                    </div>

                    <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                        <a href="{{ route('shop.index') }}" class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                            <div>
                                <p class="text-muted mb-3">Find The Best Watches for You!</p>
                                <h3 class="text-primary">Smart Watch</h3>
                                <h1 class="display-3 text-secondary mb-0">20% 
                                    <span class="text-primary fw-normal">Off</span>
                                </h1>
                            </div>
                            <img src="{{ asset('assets/img/product-2.png') }}" class="img-fluid" alt="Smart Watch">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Products Offer End -->

        <!-- Our Products Start -->
        <div class="container-fluid product py-5">
            <div class="container py-5">
                <div class="tab-class">
                    <div class="row g-4">
                        <div class="col-lg-4 text-start wow fadeInLeft" data-wow-delay="0.1s">
                            <h1>Our Products</h1>
                        </div>
                        <div class="col-lg-8 text-end wow fadeInRight" data-wow-delay="0.1s">
                            <ul class="nav nav-pills d-inline-flex text-center mb-5">
                                <li class="nav-item mb-4">
                                    <a class="d-flex mx-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill"
                                        href="#tab-1">
                                        <span class="text-dark" style="width: 130px;">All Products</span>
                                    </a>
                                </li>
                                <li class="nav-item mb-4">
                                    <a class="d-flex py-2 mx-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-2">
                                        <span class="text-dark" style="width: 130px;">New Arrivals</span>
                                    </a>
                                </li>
                                <li class="nav-item mb-4">
                                    <a class="d-flex mx-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-3">
                                        <span class="text-dark" style="width: 130px;">Featured</span>
                                    </a>
                                </li>
                                <li class="nav-item mb-4">
                                    <a class="d-flex mx-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-4">
                                        <span class="text-dark" style="width: 130px;">Top Selling</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content">
                        <!-- All Products -->
                        <div id="tab-1" class="tab-pane fade show active p-0">
                            <div class="row g-4">
                                @foreach($allProducts as $product)
                                    @include('partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </div>

                        <!-- New Arrivals -->
                        <div id="tab-2" class="tab-pane fade p-0">
                            <div class="row g-4">
                                @foreach($newProducts as $product)
                                    @include('partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </div>

                        <!-- Featured -->
                        <div id="tab-3" class="tab-pane fade p-0">
                            <div class="row g-4">
                                @foreach($featuredProducts as $product)
                                    @include('partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </div>

                        <!-- Top Selling -->
                        <div id="tab-4" class="tab-pane fade p-0">
                            <div class="row g-4">
                                @foreach($bestSellerProducts as $product)
                                    @include('partials.product-card', ['product' => $product])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Products End -->

        <!-- Product Banner Start -->
        <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-4">

            {{-- LEFT CARD (pakai product-banner-2.jpg) --}}
            @if($leftBannerProduct)
                @php
                $lp = $leftBannerProduct;
                $lpDisc = (int) ($lp->discount_percent ?? 0);
                $lpHasDisc = $lpDisc > 0;
                $lpFinal = (int) ($lp->final_price ?? 0);
                $lpFinalText = 'Rp' . number_format($lpFinal, 0, ',', '.');
                $lpOriginal = (int) ($lp->original_price ?? 0);
                $lpOriginalText = 'Rp' . number_format($lpOriginal, 0, ',', '.');
                $leftBg = asset('storage/img/product-banner-2.jpg');
                @endphp

                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                <a href="{{ route('shop.index', $lp->id) }}">
                    <div class="bg-primary rounded position-relative">
                    <img src="{{ $leftBg }}" class="img-fluid w-100 rounded" alt="{{ $lp->name }}">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                        style="background: rgba(255,255,255,0.5);">
                        <h3 class="display-5 text-primary mb-2">
                        {{ $lp->category->name ?? 'Uncategorized' }}
                        </h3>

                        <h4 class="text-dark fw-semibold mb-2">{{ $lp->name }}</h4>

                        <div class="mb-3">
                        @if($lpHasDisc)
                            <span class="text-muted fs-5 me-2" style="text-decoration: line-through;">
                            {{ $lpOriginalText }}
                            </span>
                        @endif
                        <span class="fs-4 text-primary fw-bold">{{ $lpFinalText }}</span>
                        @if($lpHasDisc)
                            <span class="badge bg-danger ms-2">-{{ $lpDisc }}%</span>
                        @endif
                        </div>

                        <span class="btn btn-primary rounded-pill align-self-start py-2 px-4">
                        Shop Now
                        </span>
                    </div>
                    </div>
                </a>
                </div>
            @endif

            {{-- RIGHT CARD (pakai product-banner.jpg) --}}
            @if($rightBannerProduct)
                @php
                $rp = $rightBannerProduct;
                $rpDisc = (int) ($rp->discount_percent ?? 0);
                $rpHasDisc = $rpDisc > 0;
                $rpFinal = (int) ($rp->final_price ?? 0);
                $rpFinalText = 'Rp' . number_format($rpFinal, 0, ',', '.');
                $rpOriginal = (int) ($rp->original_price ?? 0);
                $rpOriginalText = 'Rp' . number_format($rpOriginal, 0, ',', '.');
                $rightBg = asset('storage/img/product-banner.jpg');
                @endphp

                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.2s">
                <a href="{{ route('shop.index', $rp->id) }}">
                    <div class="text-center bg-primary rounded position-relative">
                    <img src="{{ $rightBg }}" class="img-fluid w-100 rounded" alt="{{ $rp->name }}">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                        style="background: rgba(242,139,0,0.5);">
                        <h2 class="display-6 text-secondary mb-1">{{ $rp->category->name ?? 'Uncategorized' }}</h2>
                        <h4 class="display-6 text-white fw-semibold mb-3">{{ $rp->name }}</h4>

                        <div class="mb-4">
                        @if($rpHasDisc)
                            <span class="text-white-50 fs-5 me-2" style="text-decoration: line-through;">
                            {{ $rpOriginalText }}
                            </span>
                        @endif
                        <span class="fs-3 text-white fw-bold">{{ $rpFinalText }}</span>
                        @if($rpHasDisc)
                            <span class="badge bg-dark ms-2">-{{ $rpDisc }}%</span>
                        @endif
                        </div>

                        <span class="btn btn-secondary rounded-pill align-self-center py-2 px-4">
                        Shop Now
                        </span>
                    </div>
                    </div>
                </a>
                </div>
            @endif

            </div>
        </div>
        </div>
 
    </x-slot>
    @push('scripts')
    <script>
    </script>
    @endpush
</x-app-layout>