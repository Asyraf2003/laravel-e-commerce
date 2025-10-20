<x-app-layout>
    @push('styles')
        <style>
            #dest-results .result-item:hover { background-color: #f1f5f9; }
        </style>
    @endpush

    <div class="container py-5">
        @if ($errors->any()) <div class="alert alert-danger mb-4"><ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div> @endif

        <div class="row g-4">
            {{-- Kolom Kiri: Form --}}
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Alamat Pengiriman</h5></div>
                    <div class="card-body">
                        {{-- **DIPERBAIKI:** Menggunakan nama rute yang benar 'user.checkout.store' --}}
                        <form id="checkout-form" method="POST" action="{{ route('user.checkout.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="recipient_name" class="form-label">Nama Penerima</label>
                                    <input id="recipient_name" name="recipient_name" required class="form-control" value="{{ old('recipient_name', auth()->user()->name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="recipient_phone" class="form-label">No. HP</label>
                                    <input id="recipient_phone" name="recipient_phone" required class="form-control" value="{{ old('recipient_phone') }}">
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <h6 class="mb-2">Tujuan Pengiriman</h6>
                                        <label for="dest-search" class="form-label">Cari kota/kecamatan</label>
                                        <input id="dest-search" type="text" class="form-control" placeholder="cth: mataram, denpasar">
                                        <div id="dest-results" class="border rounded divide-y max-h-52 overflow-auto mt-2 d-none"></div>
                                        <input type="hidden" name="destination_id" id="destination_id" required>
                                        <div id="dest-picked" class="form-text text-success fw-bold mt-2"></div>
                                        <input type="hidden" name="province_name" id="province_name"><input type="hidden" name="city_name" id="city_name"><input type="hidden" name="subdistrict_name" id="subdistrict_name">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Alamat Lengkap</label>
                                    <textarea id="address" name="address" required class="form-control" rows="3">{{ old('address') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="postal_code" class="form-label">Kode Pos (opsional)</label>
                                    <input id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="courier" class="form-label">Kurir</label>
                                    <select id="courier" name="courier" required class="form-select">
                                        <option value="">-- Pilih Kurir --</option>
                                        @foreach ($couriers as $c) <option value="{{ $c }}">{{ strtoupper($c) }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div><h6 class="mb-1">Layanan & Ongkir</h6><div class="form-text">Pilih tujuan & kurir, lalu klik "Hitung Ongkir".</div></div>
                                            <button type="button" id="btn-cost" class="btn btn-outline-secondary">Hitung Ongkir</button>
                                        </div>
                                        <div id="services" class="mt-3"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="service" id="service"><input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                                <div class="col-12 d-flex justify-content-end mt-4">
                                    <button type="submit" id="btn-submit-order" class="btn btn-primary btn-lg" disabled>Buat Pesanan & Bayar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Kolom Kanan: Ringkasan --}}
            <div class="col-lg-4">
                 <div class="sticky-top" style="top: 1rem;">
                    <div class="card shadow-sm">
                        <div class="card-header"><h5 class="mb-0">Ringkasan Pesanan</h5></div>
                        <div class="card-body">
                             <ul class="list-group list-group-flush">
                                @foreach($items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $item->product_name_snapshot }} × {{ $item->qty }}</span>
                                    <span>{{ 'Rp' . number_format($item->line_total, 0, ',', '.') }}</span>
                                </li>
                                @endforeach
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold border-top pt-3">
                                    <span>Subtotal Produk</span>
                                    <span>{{ 'Rp' . number_format($subtotal, 0, ',', '.') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Ongkos Kirim</span>
                                    <span id="ongkir-text">Rp 0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold fs-5 border-top pt-3">
                                    <span>Total Pembayaran</span>
                                    <span id="total-text" class="text-primary">{{ 'Rp' . number_format($subtotal, 0, ',', '.') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const rupiah = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
        const courier = document.getElementById('courier');
        const btnCost = document.getElementById('btn-cost');
        const services = document.getElementById('services');
        const serviceInput = document.getElementById('service');
        const shippingInput = document.getElementById('shipping_cost');
        const ongkirText = document.getElementById('ongkir-text');
        const totalText = document.getElementById('total-text');
        const btnSubmit = document.getElementById('btn-submit-order');
        const subtotal = {{ $subtotal }};
        const weight = {{ $weightTotal }};
        const destSearch = document.getElementById('dest-search');
        const destResults = document.getElementById('dest-results');
        const destPicked = document.getElementById('dest-picked');
        const destInput = document.getElementById('destination_id');
        const provinceInput = document.getElementById('province_name');
        const cityInput = document.getElementById('city_name');
        const subdistrictInput = document.getElementById('subdistrict_name');
        let timer;
        destSearch.addEventListener('input', e => {
            clearTimeout(timer);
            const q = e.target.value.trim();
            if (q.length < 3) { destResults.classList.add('d-none'); return; }
            timer = setTimeout(async () => {
                destResults.innerHTML = '<div class="p-2 text-muted">Mencari...</div>';
                destResults.classList.remove('d-none');
                // **DIPERBAIKI:** Menggunakan nama rute yang benar 'user.checkout.destination.search'
                const data = await (await fetch(`{{ route('user.checkout.destination.search') }}?q=${encodeURIComponent(q)}`)).json();
                if (!Array.isArray(data) || data.length === 0) { destResults.innerHTML = '<div class="p-2 text-muted">Tidak ditemukan.</div>'; return; }
                destResults.innerHTML = '';
                data.forEach(row => {
                    const div = document.createElement('div');
                    div.className = 'p-2 result-item'; div.style.cursor = 'pointer';
                    const label = `${row.subdistrict_name}, ${row.city_name}, ${row.province_name}`;
                    div.textContent = label;
                    div.addEventListener('click', () => {
                        destInput.value = row.id; provinceInput.value = row.province_name; cityInput.value = row.city_name; subdistrictInput.value = row.subdistrict_name;
                        destPicked.textContent = `Tujuan: ${label}`; destResults.classList.add('d-none');
                        serviceInput.value = ''; shippingInput.value = 0; updateTotals(); services.innerHTML = '';
                    });
                    destResults.appendChild(div);
                });
            }, 400);
        });
        btnCost.addEventListener('click', async () => {
            if (!destInput.value || !courier.value) { alert('Pilih tujuan dan kurir dulu.'); return; }
            services.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div></div>';
            // **DIPERBAIKI:** Menggunakan nama rute yang benar 'user.checkout.shipping_costs'
            const res = await fetch(`{{ route('user.checkout.shipping_costs') }}`, {
                method: 'POST', headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({ destination_id: +destInput.value, courier: courier.value, weight: weight || 1 })
            });
            const data = await res.json();
            if (!Array.isArray(data.options) || data.options.length === 0) { services.innerHTML = '<div class="alert alert-warning">Tidak ada layanan.</div>'; return; }
            services.innerHTML = '';
            data.options.forEach((opt, idx) => {
                const id = `svc_${idx}`;
                services.innerHTML += `<div class="form-check border rounded p-3"><input class="form-check-input" type="radio" name="svc" id="${id}" value="${opt.service}" data-cost="${opt.value}"><label class="form-check-label w-100" for="${id}"><div class="d-flex justify-content-between"><div><div class="fw-bold">${(opt.courier || '').toUpperCase()} - ${opt.service}</div><div class="text-sm text-muted">${opt.description || ''} ${opt.etd ? `(ETD ${opt.etd} hari)` : ''}</div></div><div class="fw-bold">${rupiah(opt.value)}</div></div></label></div>`;
            });
            document.querySelectorAll('input[name="svc"]').forEach(r => {
                r.addEventListener('change', e => {
                    serviceInput.value = e.target.value; shippingInput.value = +e.target.dataset.cost;
                    updateTotals();
                    btnSubmit.disabled = false; // Aktifkan tombol bayar
                });
            });
        });
        function updateTotals() {
            const ship = +shippingInput.value || 0;
            ongkirText.textContent = rupiah(ship); totalText.textContent = rupiah(subtotal + ship);
        }
    });
    </script>
    @endpush
</x-app-layout>

