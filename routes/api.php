<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\POSController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Quotation API routes
Route::prefix('quotations')->group(function () {
    Route::get('/pending', [QuotationController::class, 'getPending']);
    Route::get('/{id}/check-stock', [QuotationController::class, 'checkStock']);
    Route::post('/{id}/convert', [QuotationController::class, 'convert']);
});

// POS API routes for quotations
Route::prefix('pos')->group(function () {
    Route::get('/quotations/pending', [POSController::class, 'getPendingQuotations']);
    Route::post('/quotations/load', [POSController::class, 'loadQuotation']);
    Route::get('/items/{item_id}/alternative-batches', [POSController::class, 'getAlternativeBatches']);
    Route::post('/quotations/convert-to-sale', [POSController::class, 'convertQuotationToSale']);
});

// Items API for quotation builder
Route::prefix('items')->group(function () {
    Route::get('/{item}/batches', [App\Http\Controllers\ItemController::class, 'getBatches']);
    Route::get('/{item_id}/alternative-batches', [POSController::class, 'getAlternativeBatches']);
});