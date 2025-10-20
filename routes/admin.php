<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ProductImageAdminController;
use App\Http\Controllers\Admin\ProductReviewAdminController;
use App\Http\Controllers\Admin\ReviewVoteAdminController;
use App\Http\Controllers\Admin\CartAdminController;
use App\Http\Controllers\Admin\CheckoutAdminController;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/users', [UserAdminController::class, 'index'])
    ->middleware('can:viewAny,App\Models\User')
    ->name('users.index');

Route::patch('/users/{user}', [UserAdminController::class, 'update'])
    ->middleware('can:update,user')
    ->name('users.update');

Route::patch('/users/{user}/role', [UserAdminController::class, 'changeRole'])
    ->middleware('can:changeRole,user')
    ->name('users.changeRole');

Route::get('/categories', [CategoryAdminController::class, 'index'])
    ->middleware('can:viewAny,App\Models\Category')
    ->name('categories.index');

Route::get('/categories/create', [CategoryAdminController::class, 'create'])
    ->middleware('can:create,App\Models\Category')
    ->name('categories.create');

Route::post('/categories', [CategoryAdminController::class, 'store'])
    ->middleware('can:create,App\Models\Category')
    ->name('categories.store');

Route::get('/categories/{category}/edit', [CategoryAdminController::class, 'edit'])
    ->middleware('can:update,category')
    ->name('categories.edit');

Route::patch('/categories/{category}', [CategoryAdminController::class, 'update'])
    ->middleware('can:update,category')
    ->name('categories.update');

Route::patch('/categories/{category}/toggle', [CategoryAdminController::class, 'toggleActive'])
    ->middleware('can:update,category')
    ->name('categories.toggle');

Route::delete('/categories/{category}', [CategoryAdminController::class, 'destroy'])
    ->middleware('can:delete,category')
    ->name('categories.destroy');

Route::get('/products', [ProductAdminController::class, 'index'])
    ->middleware('can:viewAny,App\Models\Product')
    ->name('products.index');

Route::get('/products/create', [ProductAdminController::class, 'create'])
    ->middleware('can:create,App\Models\Product')
    ->name('products.create');

Route::post('/products', [ProductAdminController::class, 'store'])
    ->middleware('can:create,App\Models\Product')
    ->name('products.store');

Route::get('/products/{product}/edit', [ProductAdminController::class, 'edit'])
    ->middleware('can:update,product')
    ->name('products.edit');

Route::patch('/products/{product}', [ProductAdminController::class, 'update'])
    ->middleware('can:update,product')
    ->name('products.update');

Route::patch('/products/{product}/toggle/{field}', [ProductAdminController::class, 'toggle'])
    ->middleware('can:update,product')
    ->name('products.toggle');

Route::delete('/products/{product}', [ProductAdminController::class, 'destroy'])
    ->middleware('can:delete,product')
    ->name('products.destroy');

Route::get('/products/{product}/images', [ProductImageAdminController::class, 'index'])
    ->middleware('can:update,product')
    ->name('products.images.index');

Route::post('/products/{product}/images', [ProductImageAdminController::class, 'store'])
    ->middleware('can:update,product')
    ->name('products.images.store');

Route::patch('/products/{product}/images/{image}', [ProductImageAdminController::class, 'update'])
    ->middleware('can:update,product')
    ->name('products.images.update');

Route::patch('/products/{product}/images/{image}/primary', [ProductImageAdminController::class, 'makePrimary'])
    ->middleware('can:update,product')
    ->name('products.images.primary');

Route::patch('/products/{product}/images/reorder', [ProductImageAdminController::class, 'reorder'])
    ->middleware('can:update,product')
    ->name('products.images.reorder');

Route::delete('/products/{product}/images/{image}', [ProductImageAdminController::class, 'destroy'])
    ->middleware('can:update,product')
    ->name('products.images.destroy');

Route::get('/reviews', [ProductReviewAdminController::class, 'index'])
    ->middleware('can:viewAny,App\Models\ProductReview')
    ->name('reviews.index');

Route::get('/reviews/{review}/edit', [ProductReviewAdminController::class, 'edit'])
    ->middleware('can:update,review')
    ->name('reviews.edit');

Route::patch('/reviews/{review}', [ProductReviewAdminController::class, 'update'])
    ->middleware('can:update,review')
    ->name('reviews.update');

Route::patch('/reviews/{review}/approve', [ProductReviewAdminController::class, 'approve'])
    ->middleware('can:update,review')
    ->name('reviews.approve');

Route::patch('/reviews/{review}/reject', [ProductReviewAdminController::class, 'reject'])
    ->middleware('can:update,review')
    ->name('reviews.reject');

Route::delete('/reviews/{review}', [ProductReviewAdminController::class, 'destroy'])
    ->middleware('can:delete,review')
    ->name('reviews.destroy');

Route::get('/reviews/{review}/votes', [ReviewVoteAdminController::class, 'index'])
    ->middleware('can:update,review')
    ->name('reviews.votes.index');

Route::delete('/reviews/{review}/votes/{vote}', [ReviewVoteAdminController::class, 'destroy'])
    ->middleware('can:update,review')
    ->name('reviews.votes.destroy');

Route::get('/carts', [CartAdminController::class, 'index'])
    ->middleware('can:viewAny,App\Models\CartItem')
    ->name('carts.index');

Route::get('/carts/users/{user}', [CartAdminController::class, 'user'])
    ->middleware('can:viewAny,App\Models\CartItem') // atau ganti ke can:view,user jika UserPolicy ada
    ->name('carts.user');

Route::patch('/carts/item/{item}/qty', [CartAdminController::class, 'updateQty'])
    ->middleware('can:update,item')
    ->name('carts.item.qty');

Route::patch('/carts/item/{item}/status', [CartAdminController::class, 'changeStatus'])
    ->middleware('can:update,item')
    ->name('carts.item.status');

Route::delete('/carts/item/{item}', [CartAdminController::class, 'destroy'])
    ->middleware('can:delete,item')
    ->name('carts.item.destroy');

Route::post('/carts/users/{user}/clear', [CartAdminController::class, 'clearUser'])
    ->middleware('can:viewAny,App\Models\CartItem') // atau can:update,user jika ada
    ->name('carts.user.clear');

Route::get('/checkout', [CheckoutAdminController::class, 'index'])
    ->middleware('can:viewAny,App\Models\Order')
    ->name('checkout.index');

Route::get('/checkout/{order}', [CheckoutAdminController::class, 'show'])
    ->middleware('can:view,order')
    ->name('checkout.show');

Route::delete('/checkout/{order}', [CheckoutAdminController::class, 'destroy'])
    ->middleware('can:delete,order')
    ->name('checkout.destroy');

Route::patch('/checkout/{order}/mark-paid', [CheckoutAdminController::class, 'markPaid'])
    ->middleware('can:update,order')
    ->name('checkout.markPaid');

Route::patch('/checkout/{order}/mark-unpaid', [CheckoutAdminController::class, 'markUnpaid'])
    ->middleware('can:update,order')
    ->name('checkout.markUnpaid');

Route::get('/checkout-export', [CheckoutAdminController::class, 'export'])
    ->middleware('can:viewAny,App\Models\Order')
    ->name('checkout.export');
