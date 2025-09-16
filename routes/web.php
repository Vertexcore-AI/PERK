<?php

use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BinController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\VendorItemMappingController;
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

// Phase 2: Inventory Management routes
Route::resource('grns', GRNController::class);

// Custom GRN routes
Route::put('grns/{grn}/items/{grnItem}/stored-qty', [GRNController::class, 'updateStoredQty'])
    ->name('grns.update-stored-qty');

// GRN Import routes
Route::post('grns/upload-excel', [GRNController::class, 'uploadExcel'])->name('grns.upload-excel');
Route::post('grns/resolve-mapping', [GRNController::class, 'resolveMapping'])->name('grns.resolve-mapping');
Route::post('grns/process-import', [GRNController::class, 'processImport'])->name('grns.process-import');
Route::get('grns/download-template', [GRNController::class, 'downloadTemplate'])->name('grns.download-template');

// Batch Management routes
Route::resource('batches', \App\Http\Controllers\BatchController::class)->only(['index', 'show', 'edit', 'update']);
Route::get('batches/stock-value', [\App\Http\Controllers\BatchController::class, 'stockValue'])->name('batches.stock-value');
Route::get('batches/expiring', [\App\Http\Controllers\BatchController::class, 'expiring'])->name('batches.expiring');
Route::post('batches/{batch}/generate-serials', [\App\Http\Controllers\BatchController::class, 'generateSerials'])->name('batches.generate-serials');

// Inventory routes
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/stock-by-item', [InventoryController::class, 'stockByItem'])->name('stock-by-item');
    Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
    Route::get('/stock-transfer', [InventoryController::class, 'showTransfer'])->name('transfer.show');
    Route::post('/stock-transfer', [InventoryController::class, 'processTransfer'])->name('transfer.process');
    Route::get('/stock-value', [InventoryController::class, 'stockValue'])->name('stock-value');
    Route::get('/movement-report', [InventoryController::class, 'movementReport'])->name('movement-report');
    Route::post('/adjust-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock');
    Route::get('/api/item-stock', [InventoryController::class, 'getItemStock'])->name('api.item-stock');

    // Vendor Item Mapping routes
    Route::resource('mappings', VendorItemMappingController::class)->except(['show']);
    Route::get('/mappings/{mapping}', [VendorItemMappingController::class, 'show'])->name('mappings.show');
    Route::post('/mappings/{mapping}/set-preferred', [VendorItemMappingController::class, 'setPreferred'])->name('mappings.set-preferred');
    Route::get('/api/search-items', [VendorItemMappingController::class, 'searchItems'])->name('mappings.search-items');
    Route::post('/mappings/bulk-import', [VendorItemMappingController::class, 'bulkImport'])->name('mappings.bulk-import');
});
