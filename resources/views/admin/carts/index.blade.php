<x-mazer-layout>
    {{-- Page Header Slot --}}
    <x-slot>
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        
        <div class="page-heading">
            <h3>Carts Management</h3>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cart Items List</h4>
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
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-4 pb-3 border-bottom">
                            <form method="GET" action="{{ route('admin.carts.index') }}" class="d-flex flex-wrap gap-2 align-items-center flex-grow-1">
                                <input type="search" name="q" class="form-control" value="{{ $q ?? request('q') }}" placeholder="Search product name..." style="width: auto; flex-grow: 1;">
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">— All Statuses —</option>
                                    @foreach (['in_cart', 'saved', 'removed', 'checked_out'] as $st)
                                        <option value="{{ $st }}" @selected(($status ?? request('status')) === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="user_id" class="form-control" value="{{ $userId ?? request('user_id') }}" placeholder="User ID" style="width: 120px;">
                                <input type="number" name="product_id" class="form-control" value="{{ $productId ?? request('product_id') }}" placeholder="Product ID" style="width: 120px;">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.carts.index') }}" class="btn btn-secondary">Reset</a>
                            </form>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light ms-auto">Back to Dashboard</a>
                        </div>


                        {{-- Carts Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th>Price Each</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $it)
                                        <tr>
                                            <td>#{{ $it->id }}</td>
                                            <td>
                                                @if ($it->user)
                                                    <div class="fw-bold">{{ $it->user->name }}</div>
                                                    <div class="text-muted small">{{ $it->user->email }}</div>
                                                    <a href="{{ route('admin.carts.user', $it->user) }}">View User's Cart</a>
                                                @else
                                                    <em>—</em>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $it->product_name_snapshot ?? $it->product?->name ?? '-' }}</div>
                                                <small class="text-muted">Product ID: {{ $it->product_id ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.carts.item.qty', $it) }}" class="d-flex gap-2 justify-content-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="qty" value="{{ (int) $it->qty }}" min="1" class="form-control form-control-sm" style="width: 80px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                </form>
                                            </td>
                                            <td class="text-nowrap">Rp{{ number_format((int) $it->price_each, 0, ',', '.') }}</td>
                                            <td class="text-nowrap fw-bold">Rp{{ number_format((int) $it->line_total, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $status_color = match($it->status) {
                                                        'in_cart' => 'primary',
                                                        'saved' => 'info',
                                                        'checked_out' => 'success',
                                                        'removed' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-light-{{$status_color}} mb-2">{{ ucfirst(str_replace('_', ' ', $it->status)) }}</span>
                                                <form method="POST" action="{{ route('admin.carts.item.status', $it) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="input-group input-group-sm">
                                                        <select name="status" class="form-select">
                                                            @foreach (['in_cart', 'saved', 'removed'] as $st)
                                                                <option value="{{ $st }}" @selected($it->status === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-outline-secondary">Set</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.carts.item.destroy', $it) }}"
                                                    onsubmit="return confirm('This will set the item status to \'removed\'. Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">
                                                <div class="alert alert-light text-center my-3" role="alert">
                                                    No cart items found.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $items->onEachSide(1)->links() }}
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </x-slot>
</x-mazer-layout>