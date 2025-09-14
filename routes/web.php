<?php

use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BinController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard.index');
})->name('dashboard');

// Resource routes for Phase 1 entities
Route::resource('vendors', VendorController::class);
Route::resource('categories', CategoryController::class);
Route::resource('stores', StoreController::class);
Route::resource('bins', BinController::class);
Route::resource('customers', CustomerController::class);
Route::resource('items', ItemController::class);
