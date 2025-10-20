<x-mazer-layout>
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Categories Management</h3>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Category List</h4>
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
                            <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                                <input type="search" class="form-control" name="q" value="{{ $q ?? request('q') }}" placeholder="Search name/slug..." style="width: auto; flex-grow: 1;">
                                <select name="parent_id" class="form-select" style="width: auto;">
                                    <option value="">-- All Parents --</option>
                                    <option value="0" @selected($parentId === 0)>Root (no parent)</option>
                                    @foreach ($parents as $p)
                                        <option value="{{ $p->id }}" @selected($parentId === $p->id)>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="only_active" value="1" id="onlyActiveCheck" @checked($onlyAct)>
                                    <label class="form-check-label" for="onlyActiveCheck">
                                        Only Active
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Reset</a>
                            </form>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4 border-top pt-3">
                            @can('create', \App\Models\Category::class)
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-lg"></i> Add New Category
                                </a>
                            @endcan
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Back to Admin Dashboard</a>
                        </div>

                        {{-- Categories Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Parent</th>
                                        <th class="text-center">Active</th>
                                        <th class="text-center">Sort</th>
                                        <th class="text-center">Children</th>
                                        <th class="text-center">Products</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $c)
                                        <tr>
                                            <td>{{ $c->id }}</td>
                                            <td>
                                                @if ($c->image)
                                                    <img src="{{ asset('storage/'.$c->image) }}" alt="Image for {{ $c->name }}" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <strong>{{ $c->name }}</strong>
                                            </td>
                                            <td>{{ $c->slug }}</td>
                                            <td>{{ $c->parent?->name ?? '—' }}</td>
                                            <td class="text-center">
                                                <form method="POST" action="{{ route('admin.categories.toggle', $c) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $c->is_active ? 'btn-success' : 'btn-warning' }}">
                                                        {{ $c->is_active ? 'Active' : 'Inactive' }}
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-center">{{ $c->sort_order }}</td>
                                            <td class="text-center"><span class="badge bg-light-secondary">{{ $c->children_count }}</span></td>
                                            <td class="text-center"><span class="badge bg-light-info">{{ $c->products_count }}</span></td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @can('update', $c)
                                                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    @endcan
                                                    @can('delete', $c)
                                                        <form method="POST" action="{{ route('admin.categories.destroy', $c) }}"
                                                            onsubmit="return confirm('Are you sure you want to delete this category?');" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
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
                                                    No data available.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $categories->onEachSide(1)->links() }}
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </x-slot>
</x-mazer-layout>