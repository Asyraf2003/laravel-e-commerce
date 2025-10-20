<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat pesanan pengguna.
     */
    public function index()
    {
        // Ambil semua pesanan milik pengguna yang sedang login
        // Urutkan dari yang paling baru
        // Eager load relasi 'items' dan 'product' untuk menghindari N+1 query
        // Gunakan paginasi untuk menampilkan 10 pesanan per halaman
        $orders = Order::where('user_id', Auth::id())
                        ->with('items.product.primaryImage')
                        ->latest('placed_at')
                        ->paginate(10);

        // Kirim data pesanan ke view
        return view('history.index', compact('orders'));
    }

    /**
     * Menampilkan halaman detail untuk satu pesanan.
     */
    public function show(Order $order)
    {
        // Gunakan policy untuk memastikan pengguna hanya bisa melihat pesanannya sendiri
        $this->authorize('view', $order);

        // Eager load relasi yang dibutuhkan di halaman detail
        $order->load('items.product.primaryImage');

        // Kirim data pesanan ke view detail
        return view('history.show', compact('order'));
    }
}

