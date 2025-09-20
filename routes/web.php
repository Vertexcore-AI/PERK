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
use App\Http\Controllers\SalesController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
Route::get('/dashboard-weekly', [DashboardController::class, 'getWeeklySummary'])->name('dashboard.weekly');

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

// Phase 3: Sales Management routes
Route::resource('sales', SalesController::class)->except(['edit', 'update']);
Route::get('sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
Route::get('sales/export', [SalesController::class, 'export'])->name('sales.export');

// POS Routes
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [POSController::class, 'index'])->name('index');
    Route::get('/receipt', [POSController::class, 'generateReceipt'])->name('receipt');
    Route::get('/print/{saleId}', [POSController::class, 'printReceipt'])->name('print');
    Route::get('/dashboard-data', [POSController::class, 'getDashboardData'])->name('dashboard-data');

    // Invoice routes
    Route::get('/invoice/{sale}/normal', [POSController::class, 'generateInvoice'])
        ->defaults('type', 'normal')
        ->name('invoice.normal');
    Route::get('/invoice/{sale}/vat', [POSController::class, 'generateInvoice'])
        ->defaults('type', 'vat')
        ->name('invoice.vat');
});

// POS API Routes (AJAX endpoints)
Route::prefix('api/pos')->name('api.pos.')->group(function () {
    Route::post('/search-items', [POSController::class, 'searchItems'])->name('search-items');
    Route::get('/batches/{item}', [POSController::class, 'getBatches'])->name('batches');
    Route::post('/preview-batch-selection', [POSController::class, 'previewBatchSelection'])->name('preview-batch-selection');
    Route::post('/calculate-total', [POSController::class, 'calculateTotal'])->name('calculate-total');
    Route::post('/validate-stock', [POSController::class, 'validateStock'])->name('validate-stock');
    Route::post('/search-customers', [POSController::class, 'searchCustomers'])->name('search-customers');
    Route::post('/quick-create-customer', [POSController::class, 'quickCreateCustomer'])->name('quick-create-customer');
    Route::post('/process-sale', [POSController::class, 'processSale'])->name('process-sale');

    // Quotation-related POS routes
    Route::get('/quotations/pending', [POSController::class, 'getPendingQuotations'])->name('quotations.pending');
    Route::post('/quotations/load', [POSController::class, 'loadQuotation'])->name('quotations.load');
    Route::get('/alternative-batches', [POSController::class, 'getAlternativeBatches'])->name('alternative-batches');
    Route::post('/quotations/convert-to-sale', [POSController::class, 'convertQuotationToSale'])->name('quotations.convert');
});

// Phase 4: Quotation Management routes
Route::resource('quotations', QuotationController::class);
Route::post('quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');
Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
Route::get('quotations/{quotation}/check-stock', [QuotationController::class, 'checkStock'])->name('quotations.check-stock');
Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');

// Backup and Restore Management routes
Route::prefix('backups')->name('backups.')->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('index');
    Route::get('/create', [BackupController::class, 'create'])->name('create');
    Route::post('/', [BackupController::class, 'store'])->name('store');
    Route::get('/{filename}/download', [BackupController::class, 'download'])->name('download');
    Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
    Route::get('/{filename}/info', [BackupController::class, 'info'])->name('info');
    Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
    Route::post('/restore-external', [BackupController::class, 'restoreFromExternal'])->name('restore-external');
    Route::post('/upload', [BackupController::class, 'upload'])->name('upload');
    Route::post('/cleanup', [BackupController::class, 'cleanup'])->name('cleanup');
    Route::get('/restore-history', [BackupController::class, 'restoreHistory'])->name('restore-history');
    Route::post('/validate', [BackupController::class, 'validate'])->name('validate');
});
