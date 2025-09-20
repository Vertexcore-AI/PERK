<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\InventoryStock;
use App\Services\BatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    protected $batchService;

    public function __construct(BatchService $batchService)
    {
        $this->batchService = $batchService;
    }

    /**
     * Display a listing of batches
     */
    public function index(Request $request)
    {
        $query = Batch::with(['item.category', 'vendor', 'inventoryStock']);

        // Filter by item
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'available':
                    $query->whereHas('inventoryStock', function ($q) {
                        $q->where('quantity', '>', 0);
                    });
                    break;
                case 'depleted':
                    $query->whereDoesntHave('inventoryStock', function ($q) {
                        $q->where('quantity', '>', 0);
                    });
                    break;
                case 'low_stock':
                    $query->whereHas('item', function ($q) {
                        $q->where('min_stock', '>', 0);
                    })->whereHas('inventoryStock', function ($q) {
                        $q->whereRaw('quantity <= (SELECT min_stock FROM items WHERE items.id = inventory_stock.item_id)');
                    });
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_no', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($itemQuery) use ($search) {
                      $itemQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('item_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $batches = $query->paginate(15)->appends($request->query());

        // Statistics
        $totalBatches = Batch::count();
        $activeBatches = Batch::whereHas('inventoryStock', function ($q) {
            $q->where('quantity', '>', 0);
        })->count();
        $lowStockBatches = Batch::whereHas('item', function ($q) {
            $q->where('min_stock', '>', 0);
        })->whereHas('inventoryStock', function ($q) {
            $q->whereRaw('quantity <= (SELECT min_stock FROM items WHERE items.id = inventory_stock.item_id)');
        })->count();
        $totalValue = $this->calculateTotalBatchValue();

        // Filter options
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('batches.index', compact(
            'batches', 'totalBatches', 'activeBatches', 'lowStockBatches',
            'totalValue', 'items', 'vendors'
        ));
    }

    /**
     * Display the specified batch
     */
    public function show(Batch $batch)
    {
        $batch->load([
            'item.category',
            'vendor',
            'inventoryStock.store',
            'inventoryStock.bin',
            'grnItems.grn',
            'serialItems'
        ]);

        // Get movement history (sales, returns, transfers)
        $movements = $this->getBatchMovements($batch->id);

        // Calculate statistics
        $totalReceived = $batch->received_qty;
        $currentStock = $batch->inventoryStock->sum('quantity');
        $totalSold = $totalReceived - $currentStock;
        $stockValue = $currentStock * $batch->unit_cost;

        return view('batches.show', compact(
            'batch', 'movements', 'totalReceived', 'currentStock',
            'totalSold', 'stockValue'
        ));
    }

    /**
     * Show the form for editing the specified batch
     */
    public function edit(Batch $batch)
    {
        $batch->load(['item', 'vendor']);

        return view('batches.edit', compact('batch'));
    }

    /**
     * Update the specified batch
     */
    public function update(Request $request, Batch $batch)
    {
        $request->validate([
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $batch->update([
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('batches.show', $batch)
            ->with('success', 'Batch updated successfully.');
    }

    /**
     * Generate serial numbers for a batch
     */
    public function generateSerials(Request $request, Batch $batch)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
            'prefix' => 'nullable|string|max:10',
        ]);

        try {
            $serialNumbers = $this->batchService->generateSerialNumbers(
                $batch->id,
                $request->quantity,
                $request->prefix
            );

            return response()->json([
                'success' => true,
                'message' => "Generated {$request->quantity} serial numbers successfully.",
                'count' => count($serialNumbers)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating serial numbers: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get stock value report by batch
     */
    public function stockValue()
    {
        $batches = Batch::with(['item', 'vendor', 'inventoryStock'])
            ->whereHas('inventoryStock', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->get()
            ->map(function ($batch) {
                $currentStock = $batch->inventoryStock->sum('quantity');
                $value = $currentStock * $batch->unit_cost;

                return [
                    'batch' => $batch,
                    'current_stock' => $currentStock,
                    'unit_cost' => $batch->unit_cost,
                    'total_value' => $value,
                ];
            })
            ->sortByDesc('total_value');

        $totalValue = $batches->sum('total_value');
        $totalBatches = $batches->count();

        return view('batches.stock-value', compact('batches', 'totalValue', 'totalBatches'));
    }

    /**
     * Show expiring batches
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 30);

        $expiringBatches = Batch::with(['item', 'vendor', 'inventoryStock'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->whereHas('inventoryStock', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return view('batches.expiring', compact('expiringBatches', 'days'));
    }

    /**
     * Calculate total value of all batches
     */
    private function calculateTotalBatchValue()
    {
        return Batch::whereHas('inventoryStock', function ($q) {
            $q->where('quantity', '>', 0);
        })->get()->sum(function ($batch) {
            return $batch->inventoryStock->sum('quantity') * $batch->unit_cost;
        });
    }

    /**
     * Get batch movement history
     */
    private function getBatchMovements($batchId)
    {
        // This would need to be implemented based on your sales/returns/transfers tables
        // For now, returning empty array
        return collect([]);
    }
}