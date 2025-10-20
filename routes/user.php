<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentWebhookController;
use App\Http\Controllers\User\OrderPaymentController;
use App\Http\Controllers\User\HistoryController;

Route::get('/me', fn() => to_route('home'))->name('user.dashboard');

Route::get('/cart', [CartController::class, 'index'])
    ->middleware('can:viewAny,App\Models\CartItem')
    ->name('cart.index');

Route::post('/cart/{product}', [CartController::class, 'add'])
    ->middleware('can:create,App\Models\CartItem')
    ->name('cart.add');

Route::patch('/cart/item/{item}', [CartController::class, 'update'])
    ->middleware('can:update,item')
    ->name('cart.update');

Route::delete('/cart/item/{item}', [CartController::class, 'remove'])
    ->middleware('can:delete,item')
    ->name('cart.remove');

Route::post('/cart/clear', [CartController::class, 'clear'])
    ->middleware('can:viewAny,App\Models\CartItem')
    ->name('cart.clear');

Route::get('/cart/summary', [CartController::class, 'summary'])
    ->middleware('can:viewAny,App\Models\CartItem')
    ->name('cart.summary');
    
Route::get('/wishlist', [WishlistController::class, 'index'])
    ->middleware('can:viewAny,App\Models\Wishlist')
    ->name('wishlist.index');

Route::get('/wishlist/count', [WishlistController::class, 'count'])
    ->middleware('can:viewAny,App\Models\Wishlist')
    ->name('wishlist.count');

Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])
    ->middleware('can:create,App\Models\Wishlist')
    ->name('wishlist.toggle');

Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])
    ->middleware('can:delete,wishlist')
    ->name('wishlist.destroy');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/checkout/destination/search', [CheckoutController::class, 'searchDestination'])->name('checkout.destination.search');
Route::post('/checkout/shipping-costs', [CheckoutController::class, 'getShippingCosts'])->name('checkout.shipping_costs');

Route::post('/payments/midtrans-notification', [PaymentWebhookController::class, 'handle']);

Route::get('/orders/{order}/finish', [OrderPaymentController::class, 'finish'])->name('orders.finish');
Route::get('/orders/{order}/refresh', [OrderPaymentController::class, 'refresh'])->name('orders.refresh');

Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
Route::get('/history/{order}', [HistoryController::class, 'show'])->name('history.show');