<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\DetailController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{id}', [DetailController::class, 'detail'])->name('shop.detail');
Route::get('/contact', fn() => view('contact'))->name('contact');

Route::middleware(['auth'])->get('/dashboard', function () {
    
    /** @var \App\Models\User $user */ 

    $user = Auth::user();

    $roleValue = $user->roleString();

    return match ($roleValue) {
        'admin' => to_route('admin.dashboard'),
        'other' => to_route('other.dashboard'),
        default => to_route('home'),
    };
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';