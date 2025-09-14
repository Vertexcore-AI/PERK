<?php

use App\Http\Controllers\BinController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard.index');
})->name('dashboard');

Route::resource('vendors', VendorController::class);
Route::resource('categories', CategoryController::class);
Route::resource('stores', StoreController::class);
Route::resource('bins', BinController::class);

//export vendors
Route::get('vendors-export', [VendorController::class, 'exportCsv'])->name('vendors.export');
//export categories
Route::get('categories-export', [CategoryController::class, 'exportCsv'])->name('categories.export');
//export bins
Route::get('bins-export', [BinController::class, 'exportCsv'])->name('bins.export');
//export stores
Route::get('stores-export', [StoreController::class, 'exportCsv'])->name('stores.export');
