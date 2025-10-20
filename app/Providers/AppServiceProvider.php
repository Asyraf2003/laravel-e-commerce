<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Wishlist;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $wishlistCount = 0;
            $cartCount = 0;
            
            if (Auth::check()) {
                $uid = Auth::id();
                $wishlistCount = Wishlist::where('user_id', $uid)->count();
                $cartCount = CartItem::where('user_id', $uid)
                    ->where('status', 'in_cart')
                    ->sum('qty');
            }

            $view->with(compact('wishlistCount', 'cartCount'));
        });
    }
}
