<x-app-layout>
    {{-- Header opsional, pakai slot yang bener --}}
    <x-slot name="header">
        <div class="container py-3">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0">My Wishlist</h3>
                <a href="{{ route('user.wishlist.index') }}" class="btn btn-light">
                    <i class="fa fa-refresh me-2"></i>Refresh
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container py-5">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($products->count() === 0)
            <div class="text-center py-5">
                <i class="fa fa-heart-o fa-3x mb-3 text-muted"></i>
                <h5 class="mb-2">Wishlist kamu kosong</h5>
                <p class="text-muted mb-4">Klik ikon hati pada halaman produk untuk menyimpan ke wishlist.</p>
                <a href="{{ route('shop.index') ?? '#' }}" class="btn btn-primary">
                    <i class="fa fa-shopping-bag me-2"></i> Jelajahi Produk
                </a>
            </div>
        @else
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show active p-0 product">
                <div class="row g-4">
                    @foreach ($products as $product)
                        @include('partials.product-card', [
                            'product' => $product,
                            'isWishlisted' => true, 
                        ])
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $products->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
