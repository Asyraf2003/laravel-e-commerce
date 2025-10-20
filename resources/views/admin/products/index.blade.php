<x-mazer-layout>
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Products Management</h3>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Product List</h4>
                    </div>
                    <div class="card-body">

                        {{-- Session Status Message --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4 class="alert-heading">There were some problems with your input.</h4>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Filter and Actions Toolbar --}}
                        <div class="mb-4">
                            <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                                <input type="search" class="form-control" name="q" value="{{ $q ?? request('q') }}" placeholder="Search name/slug/sku..." style="width: auto; flex-grow: 1;">
                                <select name="category_id" class="form-select" style="width: auto;">
                                    <option value="">— All Categories —</option>
                                    @foreach ($categoryOptions as $opt)
                                        <option value="{{ $opt['id'] }}" @selected((string)$categoryId === (string)$opt['id'])>
                                            {{ $opt['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="only_active" value="1" id="onlyActiveCheck" @checked($onlyActive)>
                                    <label class="form-check-label" for="onlyActiveCheck">
                                        Only Active
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="only_featured" value="1" id="onlyFeaturedCheck" @checked($onlyFeatured)>
                                    <label class="form-check-label" for="onlyFeaturedCheck">
                                        Only Featured
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Reset</a>
                            </form>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4 border-top pt-3">
                            @can('create', \App\Models\Product::class)
                                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-lg"></i> Add New Product
                                </a>
                            @endcan
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Back to Admin Dashboard</a>
                        </div>


                        {{-- Products Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Diskon</th>
                                        <th>Final</th>
                                        <th class="text-center">Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $p)
                                        <tr>
                                            <td>{{ $p->id }}</td>
                                            <td>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <img src="{{ asset($p->primaryImage?->image_path ?? 'path/to/default-image.jpg') }}" alt="Image of {{ $p->name }}" style="width: 45px; height: 45px; object-fit: cover;" class="rounded">
                                                    <div>
                                                        <h6 class="mb-0">{{ $p->name }}</h6>
                                                        <small class="text-muted">SKU: {{ $p->sku ?? '—' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $p->category?->name ?? '—' }}</td>
                                            <td class="text-nowrap">Rp{{ number_format((int) $p->original_price, 0, ',', '.') }}</td>
                                            <td class="text-nowrap">{{ $p->discount_percent ?? 0 }}%</td>
                                            <td class="text-nowrap fw-bold">Rp{{ number_format((int) $p->final_price, 0, ',', '.') }}</td>
                                            <td class="text-center"><span class="badge bg-light-secondary">{{ $p->stock ?? 0 }}</span></td>
                                            <td class="text-nowrap">
                                                <div class="d-flex flex-column gap-1">
                                                    <form method="POST" action="{{ route('admin.products.toggle', [$p, 'is_active']) }}" class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm w-100 {{ $p->is_active ? 'btn-success' : 'btn-warning' }}">{{ $p->is_active ? 'Active' : 'Inactive' }}</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.products.toggle', [$p, 'is_featured']) }}" class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm w-100 {{ $p->is_featured ? 'btn-info' : 'btn-secondary' }}">{{ $p->is_featured ? 'Featured' : 'Not Featured' }}</button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @can('update', $p)
                                                        <a href="{{ route('admin.products.images.index', $p) }}" class="btn btn-sm btn-light position-relative">
                                                            Images
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                                {{ $p->images_count }}
                                                            </span>
                                                        </a>
                                                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    @endcan
                                                    @can('delete', $p)
                                                        <form method="POST" action="{{ route('admin.products.destroy', $p) }}"
                                                            onsubmit="return confirm('Are you sure you want to delete this product?');" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="alert alert-light text-center my-3" role="alert">
                                                    No products found.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $products->onEachSide(1)->links() }}
                        </div>

                    </div>
                </div>
            </section>
        </div>

    </x-slot>
</x-mazer-layout>