<x-mazer-layout :pageTitle="'Dashboard E-Commerce'">
    <x-slot>
        {{-- Header dengan Tombol Burger untuk Mobile --}}
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
            
        <div class="page-heading">
            <h3>Dashboard E-Commerce</h3>
        </div> 
        <div class="page-content"> 
            <section class="row">
                {{-- Kolom Utama (9/12) --}}
                <div class="col-12 col-lg-9">
                    {{-- Baris untuk Kartu Statistik Utama (KPI) --}}
                    <div class="row">
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon purple mb-2">
                                                <i class="bi bi-cash-stack fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Pendapatan Hari Ini</h6>
                                            <h6 class="font-extrabold mb-0">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</h6>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card"> 
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon blue mb-2">
                                                <i class="bi bi-cart-check fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Pesanan Baru</h6>
                                            <h6 class="font-extrabold mb-0">{{ $newOrdersToday }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon green mb-2">
                                                <i class="bi bi-person-plus fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Pelanggan Baru</h6>
                                            <h6 class="font-extrabold mb-0">{{ $newCustomersToday }}</h6>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon red mb-2">
                                                <i class="bi bi-box-seam fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Perlu Dikirim</h6>
                                            <h6 class="font-extrabold mb-0">{{ $ordersToShip }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Baris untuk Grafik Penjualan --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Analisis Penjualan Harian (Candlestick)</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="chart-candlestick"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Baris untuk Tabel Pesanan Terbaru & Produk Terlaris --}}
                    <div class="row">
                        <div class="col-12 col-xl-8">
                             <div class="card">
                                <div class="card-header">
                                    <h4>Pesanan Terbaru</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-lg">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Pelanggan</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="#">#ORD-20251010-001</a></td>
                                                    <td class="text-bold-500">Budi Santoso</td>
                                                    <td class="text-bold-500">Rp 450.000</td>
                                                    <td><span class="badge bg-success">Selesai</span></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">#ORD-20251010-002</a></td>
                                                    <td class="text-bold-500">Citra Lestari</td>
                                                    <td class="text-bold-500">Rp 1.200.000</td>
                                                    <td><span class="badge bg-warning">Diproses</span></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">#ORD-20251010-003</a></td>
                                                    <td class="text-bold-500">Ahmad Dhani</td>
                                                    <td class="text-bold-500">Rp 250.000</td>
                                                    <td><span class="badge bg-info">Dikirim</span></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">#ORD-20251009-112</a></td>
                                                    <td class="text-bold-500">Dewi Persik</td>
                                                    <td class="text-bold-500">Rp 85.000</td>
                                                    <td><span class="badge bg-danger">Dibatalkan</span></td>
                                                </tr>
                                                 <tr>
                                                    <td><a href="#">#ORD-20251009-111</a></td>
                                                    <td class="text-bold-500">Eko Patrio</td>
                                                    <td class="text-bold-500">Rp 3.500.000</td>
                                                    <td><span class="badge bg-success">Selesai</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Produk Terlaris</h4>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span>Laptop Pro M3 14"</span> <span class="badge bg-primary rounded-pill">120 terjual</span>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span>Keyboard Mekanikal K8</span> <span class="badge bg-primary rounded-pill">98 terjual</span>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span>Mouse Gaming G-Pro</span> <span class="badge bg-primary rounded-pill">75 terjual</span>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span>Kaos "Code Everyday"</span> <span class="badge bg-primary rounded-pill">62 terjual</span>
                                        </a>
                                         <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span>Monitor Ultrawide 34"</span> <span class="badge bg-primary rounded-pill">43 terjual</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Kolom Sidebar (3/12) --}}
                <div class="col-12 col-lg-3">
                    {{-- Kartu Profil Admin --}}
                    <div class="card">
                        <div class="card-body py-4 px-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl">
                                    <img src="{{ asset('mazer/compiled/jpg/1.jpg') }}" alt="Default Picture">
                                </div>
                                <div class="ms-3 name">
                                    <h5 class="font-bold">{{ $user->name }}</h5>
                                    <h6 class="text-muted mb-0">{{ $user->email }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Aktivitas Terbaru --}}
                    <div class="card">
                        <div class="card-header">
                            <h4>Aktivitas Terbaru</h4>
                        </div>
                        <div class="card-content pb-4">
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="avatar avatar-lg bg-light-success">
                                   <span class="avatar-content"><i class="bi bi-cart-plus-fill"></i></span>
                                </div>
                                <div class="name ms-4">
                                    <h6 class="mb-1">Pesanan Baru</h6>
                                    <small class="text-muted mb-0">#ORD-20251010-002 oleh Citra L.</small>
                                </div>
                            </div>
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="avatar avatar-lg bg-light-info">
                                    <span class="avatar-content"><i class="bi bi-person-check-fill"></i></span>
                                </div>
                                <div class="name ms-4">
                                    <h6 class="mb-1">Pelanggan Baru</h6>
                                    <small class="text-muted mb-0">Rina mendaftar.</small>
                                </div>
                            </div>
                             <div class="recent-message d-flex px-4 py-3">
                                <div class="avatar avatar-lg bg-light-danger">
                                    <span class="avatar-content"><i class="bi bi-exclamation-triangle-fill"></i></span>
                                </div>
                                <div class="name ms-4">
                                    <h6 class="mb-1">Stok Hampir Habis</h6>
                                    <small class="text-muted mb-0">Mouse Gaming G-Pro (sisa 3)</small>
                                </div>
                            </div>
                            <div class="px-4">
                                <a href="#" class='btn btn-block btn-xl btn-outline-primary font-bold mt-3'>Lihat Semua Aktivitas</a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Analisis Penjualan Harian (Donut)</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-order-status"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Status Pesanan</h4>
                        </div>
                        <div class="card-body">
                            {{-- Elemen ini akan diisi oleh chart Pie/Donut dari ApexCharts atau Chart.js --}}
                            <div id="chart-order-status"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>2025 &copy; Mazer E-Commerce</p>
                </div> 
                <div class="float-end">
                    <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                        by <a href="https://saugi.me">Saugi</a></p>
                </div>
            </div>
        </footer>
    </x-slot>
    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var optionsCandlestick = {
                series: [{
                    data: [
                        // Format: [timestamp, [open, high, low, close]]
                        // Anggap saja ini data penjualan 5 hari terakhir
                        {
                            x: new Date('2025-10-06').getTime(),
                            y: [0, 550000, 50000, 10500000] // Transaksi terendah 50rb, tertinggi 550rb, total 10.5jt
                        },
                        {
                            x: new Date('2025-10-07').getTime(),
                            y: [0, 850000, 75000, 12300000] // Transaksi terendah 75rb, tertinggi 850rb, total 12.3jt
                        },
                        {
                            x: new Date('2025-10-08').getTime(),
                            y: [0, 450000, 40000, 9800000]  // Transaksi terendah 40rb, tertinggi 450rb, total 9.8jt
                        },
                        {
                            x: new Date('2025-10-09').getTime(),
                            y: [0, 1200000, 150000, 18500000] // Transaksi terendah 150rb, tertinggi 1.2jt, total 18.5jt
                        },
                        {
                            x: new Date('2025-10-10').getTime(),
                            y: [0, 900000, 80000, 12540000] // Transaksi terendah 80rb, tertinggi 900rb, total 12.54jt
                        }
                    ]
                }],
                chart: {
                    type: 'candlestick',
                    height: 350
                },
                title: {
                    text: 'Analisis Penjualan Harian',
                    align: 'left'
                },
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    tooltip: {
                        enabled: true
                    },
                    labels: {
                        formatter: function (value) {
                            return "Rp " + (value / 1000000).toFixed(1) + " Jt";
                        }
                    }
                }
            };

            var chartCandlestick = new ApexCharts(document.querySelector("#chart-candlestick"), optionsCandlestick);
            chartCandlestick.render();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var optionsSalesTrend = {
                series: [{
                    name: 'Pendapatan',
                    data: [9800000, 18500000, 12540000, 16200000, 14800000, 22300000, 20100000]
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'category',
                    categories: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"]
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return "Rp " + (value / 1000000).toFixed(1) + " Jt";
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },
                    y: {
                        formatter: function(val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                },
            };
            
            var chartSalesTrend = new ApexCharts(document.querySelector("#chart-sales-trend"), optionsSalesTrend);
            chartSalesTrend.render();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var optionsOrderStatus = {
                series: [150, 85, 30, 15], // Data dummy: 150 Selesai, 85 Diproses, 30 Dikirim, 15 Dibatalkan
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: ['Selesai', 'Diproses', 'Dikirim', 'Dibatalkan'],
                colors: ['#198754', '#ffc107', '#0dcaf0', '#dc3545'], // Warna sesuai badge bootstrap
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chartOrderStatus = new ApexCharts(document.querySelector("#chart-order-status"), optionsOrderStatus);
            chartOrderStatus.render();
        });
    </script>
    @endpush
</x-mazer-layout>
