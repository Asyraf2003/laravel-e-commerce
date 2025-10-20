<div class="row g-4 product">
  @forelse($products as $product)
    @include('partials.product-card', ['product' => $product])
  @empty
    <div class="col-12">
      <div class="alert alert-warning mb-0">Produk tidak ditemukan.</div>
    </div>
  @endforelse
</div>
