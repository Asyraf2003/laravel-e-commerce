<x-mazer-layout>
    {{-- Page Header Slot --}}
    <x-slot>
        
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        
        <div class="page-heading">
            <h3>Checkout Management</h3>
            <p class="text-muted mb-0">Daftar pesanan dan status pembayaran (Midtrans).</p>
        </div>

        <div class="page-content">
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Orders List</h4>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Back to Dashboard</a>
                    </div>
    
                    <div class="card-body">
                        {{-- Session Status --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
    
                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4 class="alert-heading">Ada masalah pada input.</h4>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
    
                        {{-- Toolbar Filter --}}
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-4 pb-3 border-bottom">
                            <form method="GET" action="{{ route('admin.checkout.index') }}" class="d-flex flex-wrap gap-2 align-items-center flex-grow-1">
                                <input type="search" name="q" class="form-control" value="{{ request('q') }}"
                                    placeholder="Cari order_no / penerima / phone..." style="flex:1 1 260px;">
                                <select name="transaction_status" class="form-select" style="width:auto;">
                                    @php $ts = request('transaction_status') @endphp
                                    <option value="">— Txn Status —</option>
                                    @foreach (['pending','challenge','deny','cancel','expire','failure','settlement','capture','authorize'] as $opt)
                                        <option value="{{ $opt }}" @selected($ts===$opt)>{{ ucfirst($opt) }}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="form-select" style="width:auto;">
                                    @php $st = request('status') @endphp
                                    <option value="">— Paid/Unpaid —</option>
                                    @foreach (['paid','unpaid','refunded'] as $opt)
                                        <option value="{{ $opt }}" @selected($st===$opt)>{{ ucfirst($opt) }}</option>
                                    @endforeach
                                </select>
                                <input type="date" name="from" class="form-control" value="{{ request('from') }}" style="width:auto;">
                                <input type="date" name="to" class="form-control" value="{{ request('to') }}" style="width:auto;">
                                <button class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.checkout.index') }}" class="btn btn-secondary">Reset</a>
                            </form>
    
                            {{-- (Opsional) Export --}}
                            <form action="{{ route('admin.checkout.export') }}" method="GET">
                                <input type="hidden" name="q" value="{{ request('q') }}">
                                <input type="hidden" name="transaction_status" value="{{ request('transaction_status') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="from" value="{{ request('from') }}">
                                <input type="hidden" name="to" value="{{ request('to') }}">
                                <button class="btn btn-outline-primary">Export CSV</button>
                            </form>
                        </div>
    
                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>Penerima</th>
                                    <th>Alamat</th>
                                    <th>Kurir</th>
                                    <th>Total</th>
                                    <th>Gateway</th>
                                    <th>Txn</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $o)
                                    <tr>
                                        <td>{{ ($orders->currentPage()-1)*$orders->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $o->order_no }}</div>
                                            <div class="text-muted small">{{ $o->id }}</div>
                                            <button type="button" class="btn btn-xs btn-outline-secondary mt-1"
                                                    onclick="copyToClipboard('{{ $o->order_no }}', this)">Copy</button>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $o->recipient_name ?? '-' }}</div>
                                            <div class="text-muted small">{{ $o->recipient_phone ?? '-' }}</div>
                                        </td>
                                        <td class="small">
                                            {{ strtoupper($o->subdistrict_name ?? '') }}
                                            {{ $o->subdistrict_name ? ',' : '' }}
                                            {{ strtoupper($o->city_name ?? '') }}
                                            {{ $o->city_name ? ',' : '' }}
                                            {{ strtoupper($o->province_name ?? '') }}
                                            <div class="text-muted">{{ $o->address ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="text-uppercase fw-semibold">{{ $o->courier ?? '-' }}</div>
                                            <div class="small text-muted">{{ $o->service ?? '-' }}</div>
                                        </td>
                                        <td class="text-nowrap fw-bold">
                                            {{ $o->currency ?? 'IDR' }} {{ number_format((float)($o->total ?? 0), 0, ',', '.') }}
                                            <div class="small text-muted">Ongkir: {{ number_format((float)($o->shipping_cost ?? 0), 0, ',', '.') }}</div>
                                        </td>
                                        <td class="text-uppercase small">{{ $o->payment_gateway ?? '-' }}</td>
                                        <td>
                                            @php
                                                $txn = $o->transaction_status ?? '-';
                                                $map = [
                                                'settlement'=>'success','capture'=>'success','authorize'=>'primary',
                                                'pending'=>'warning','challenge'=>'warning',
                                                'deny'=>'danger','cancel'=>'secondary','expire'=>'secondary','failure'=>'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $map[$txn] ?? 'secondary' }}">{{ $txn }}</span>
                                            <div class="small text-muted">{{ $o->fraud_status ?? '' }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $b2 = ['paid'=>'success','unpaid'=>'secondary','refunded'=>'info'][$o->status ?? ''] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $b2 }}">{{ $o->status ?? '-' }}</span>
                                        </td>
                                        <td class="small">
                                            <div>Placed: {{ optional($o->placed_at)->format('Y-m-d H:i') ?? '-' }}</div>
                                            <div>Paid: {{ optional($o->paid_at)->format('Y-m-d H:i') ?? '-' }}</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.checkout.show',$o->id) }}" class="btn btn-sm btn-primary">Detail</a>
                                            @if(!empty($o->payment_redirect_url))
                                                <button class="btn btn-sm btn-outline-secondary"
                                                        onclick="copyToClipboard('{{ $o->payment_redirect_url }}', this)">Copy URL</button>
                                            @endif
                                            <form action="{{ route('admin.checkout.destroy',$o->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus order {{ $o->order_no }}? Tindakan ini tidak dapat dibatalkan.');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11">
                                            <div class="alert alert-light text-center my-3">Tidak ada data.</div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
    
                        {{-- Pagination --}}
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $orders->onEachSide(1)->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    
    </x-slot>    

    @push('scripts')
    <script>
        function copyToClipboard(text, btn){
            navigator.clipboard.writeText(text).then(()=>{
                const t = btn.innerText; btn.innerText='Copied!';
                setTimeout(()=>btn.innerText=t, 1200);
            });
        }
    </script>
    @endpush
</x-mazer-layout>
