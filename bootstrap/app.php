<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // ADMIN
            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // USER
            Route::middleware(['web', 'auth', 'user'])
                ->prefix('user')
                ->name('user.')
                ->group(base_path('routes/user.php'));

            // OTHER
            Route::middleware(['web', 'auth', 'other'])
                ->prefix('other')
                ->name('other.')
                ->group(base_path('routes/other.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'  => \App\Http\Middleware\RoleMiddleware::class,

            'admin' => \App\Http\Middleware\AdminOnly::class,
            'user'  => \App\Http\Middleware\UserOnly::class,
            'other' => \App\Http\Middleware\OtherOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
