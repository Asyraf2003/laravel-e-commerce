<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Other\DashboardController;

Route::get('/', DashboardController::class)->name('dashboard');
