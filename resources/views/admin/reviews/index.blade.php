<x-mazer-layout :pageTitle="'Kelola Review Produk'">
    <x-slot>
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Kelola Review Produk</h3>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter & Pencarian</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reviews.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <input type="search" class="form-control" name="q" value="{{ $q ?? request('q') }}" placeholder="Cari judul/isi review...">
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <select name="status" class="form-select">
                                    <option value="">— Semua Status —</option>
                                    <option value="pending"  @selected($status==='pending')>Pending</option>
                                    <option value="approved" @selected($status==='approved')>Approved</option>
                                    <option value="rejected" @selected($status==='rejected')>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <input type="number" class="form-control" name="product_id" value="{{ $productId }}" placeholder="ID Produk">
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <input type="number" class="form-control" name="user_id" value="{{ $userId }}" placeholder="ID User">
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <select name="rating" class="form-select">
                                    <option value="">— Semua Rating —</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" @selected($rating==$i)>{{ $i }} Bintang</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2 d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="verified" id="verifiedCheck" value="1" @checked($verified===true)>
                                    <label class="form-check-label" for="verifiedCheck">
                                        Verified Only
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel-fill me-2"></i>Filter
                                </button>
                                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Review</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Produk & User</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th class="text-center">Helpful</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $r)
                                    <tr>
                                        <td>{{ $r->id }}</td>
                                        <td>
                                            <div><strong>{{ $r->product?->name ?? 'N/A' }}</strong> <small class="text-muted">(#{{ $r->product_id }})</small></div>
                                            <small class="text-muted">oleh {{ $r->user?->name ?? 'N/A' }} (#{{ $r->user_id }})</small>
                                        </td>
                                        <td class="text-nowrap" style="color: #FFD700;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $i <= $r->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </td>
                                        <td>
                                            <strong>{{ $r->title ?? '—' }}</strong>
                                            <p class="mb-0 small text-muted">{{ Str::limit($r->body, 150) }}</p>
                                        </td>
                                        <td>
                                            @if ($r->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif ($r->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                            
                                            @if($r->is_verified_purchase)
                                                <span class="badge bg-light-success mt-1 d-block">Verified Purchase</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $r->helpful_count ?? 0 }}</td>
                                        <td>
                                            <small class="text-muted d-block">Dibuat: {{ $r->created_at->format('d M Y, H:i') }}</small>
                                            @if($r->approved_at)<small class="text-success d-block">Disetujui: {{ $r->approved_at->format('d M Y') }}</small>@endif
                                            @if($r->rejected_at)<small class="text-danger d-block">Ditolak: {{ $r->rejected_at->format('d M Y') }}</small>@endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                @can('update', $r)
                                                    <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-circle"></i></button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.reviews.reject', $r) }}">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-secondary" title="Reject"><i class="bi bi-x-circle"></i></button>
                                                    </form>
                                                    <a href="{{ route('admin.reviews.edit', $r) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                @endcan
                                                @can('delete', $r)
                                                    <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Hapus review ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center">Belum ada data review.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reviews->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </section>
        
    </x-slot> 
</x-mazer-layout>