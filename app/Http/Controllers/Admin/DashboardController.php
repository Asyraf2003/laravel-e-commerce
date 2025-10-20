<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User; // 1. Jangan lupa import model User
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        // Data yang sudah ada
        $dailyRevenue = Order::where('status', Order::STATUS_PAID)
                             ->whereDate('paid_at', today()) // Lebih akurat pakai paid_at
                             ->sum('total');

        // === AWAL PENAMBAHAN ===

        // 2. Hitung Pesanan Baru Hari Ini
        $newOrdersToday = Order::whereDate('created_at', today())->count();

        // 3. Hitung Pelanggan Baru Hari Ini
        $newCustomersToday = User::whereDate('created_at', today())->count();

        // 4. Hitung Pesanan yang Perlu Dikirim (statusnya 'paid')
        $ordersToShip = Order::where('status', Order::STATUS_PAID)->count();

        // === AKHIR PENAMBAHAN ===

        // 5. Kirim semua data ke view menggunakan compact agar lebih rapi
        return view('admin.dashboard', compact(
            'user',
            'dailyRevenue', 
            'newOrdersToday', 
            'newCustomersToday', 
            'ordersToShip'
        ));
    }
}