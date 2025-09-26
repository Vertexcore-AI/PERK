<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Item;
use App\Models\Store;
use App\Models\Bin;
use App\Models\Batch;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display inventory overview
     */
    public function index(Request $request)
    {
        $query = InventoryStock::with(['item.category', 'store', 'bin', 'batch.vendor'])
            ->where('quantity', '>', 0);

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by item
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // Filter by bin
        if ($request->filled('bin_id')) {
            $query->where('bin_id', $request->bin_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_no', 'like', "%{$search}%");
            });
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'last_updated');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $inventory = $query->paginate(20)->appends($request->query());

        // Statistics
        $totalItems = InventoryStock::distinct('item_id')->count();
        $totalQuantity = InventoryStock::sum('quantity');
        $lowStockItems = $this->getLowStockCount();
        $totalValue = $this->inventoryService->getStockValue()['total_value'];

        // Filter options
        $stores = Store::orderBy('store_name')->get();
        $items = Item::where('is_active', true)->orderBy('name')->get();
        $bins = Bin::with('store')->orderBy('name')->get();

        return view('inventory.index', compact(
            'inventory', 'totalItems', 'totalQuantity', 'lowStockItems',
            'totalValue', 'stores', 'items', 'bins'
        ));
    }

    /**
     * Display stock by item summary
     */
    public function stockByItem(Request $request)
    {
        $query = Item::with(['category', 'inventoryStock.store', 'inventoryStock.batch.vendor'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by low stock
        if ($request->filled('low_stock') && $request->low_stock == '1') {
            $query->where('reorder_point', '>', 0)
                  ->whereHas('inventoryStock', function ($q) {
                      $q->havingRaw('SUM(quantity) <= items.reorder_point');
                  });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_no', 'like', "%{$search}%");
            });
        }

        $items = $query->get()->map(function ($item) {
            $totalStock = $item->inventoryStock->sum('quantity');
            $stockValue = $item->inventoryStock->sum(function ($stock) {
                return $stock->quantity * ($stock->batch->unit_cost ?? 0);
            });

            return [
                'item' => $item,
                'total_stock' => $totalStock,
                'stock_value' => $stockValue,
                'is_low_stock' => $item->reorder_point > 0 && $totalStock <= $item->reorder_point,
                'stores' => $item->inventoryStock->groupBy('store_id')->map(function ($stocks) {
                    return [
                        'store' => $stocks->first()->store,
                        'quantity' => $stocks->sum('quantity')
                    ];
                })->values()
            ];
        });

        // Sort by total stock
        if ($request->get('sort_by') == 'stock') {
            $items = $request->get('sort_order', 'desc') == 'desc'
                ? $items->sortByDesc('total_stock')
                : $items->sortBy('total_stock');
        }

        // Statistics
        $totalItems = $items->count();
        $totalStock = $items->sum('total_stock');
        $totalValue = $items->sum('stock_value');
        $lowStockCount = $items->where('is_low_stock', true)->count();

        return view('inventory.stock-by-item', compact(
            'items', 'totalItems', 'totalStock', 'totalValue', 'lowStockCount'
        ));
    }

    /**
     * Display low stock items
     */
    public function lowStock()
    {
        $lowStockItems = $this->inventoryService->generateLowStockAlert();

        $totalShortage = collect($lowStockItems)->sum('shortage');
        $criticalItems = collect($lowStockItems)->where('shortage', '>', 10)->count();

        return view('inventory.low-stock', compact(
            'lowStockItems', 'totalShortage', 'criticalItems'
        ));
    }

    /**
     * Show stock transfer form
     */
    public function showTransfer()
    {
        $stores = Store::orderBy('store_name')->get();
        $bins = Bin::with('store')->orderBy('name')->get();
        $items = Item::where('is_active', true)
            ->whereHas('inventoryStock', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('name')
            ->get();

        return view('inventory.transfer', compact('stores', 'bins', 'items'));
    }

    /**
     * Process stock transfer
     */
    public function processTransfer(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'quantity' => 'required|integer|min:1',
            'from_bin_id' => 'nullable|exists:bins,id',
            'to_bin_id' => 'nullable|exists:bins,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->inventoryService->transferStock(
                $request->from_store_id,
                $request->to_store_id,
                $request->item_id,
                $request->quantity,
                $request->from_bin_id,
                $request->to_bin_id
            );

            // Log the transfer
            $item = Item::find($request->item_id);
            $fromStore = Store::find($request->from_store_id);
            $toStore = Store::find($request->to_store_id);

            \Log::info('Stock Transfer', [
                'item' => $item->name,
                'from_store' => $fromStore->store_name,
                'to_store' => $toStore->store_name,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully transferred {$request->quantity} units of {$item->name} from {$fromStore->store_name} to {$toStore->store_name}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display stock value report
     */
    public function stockValue(Request $request)
    {
        $storeId = $request->get('store_id');
        $stockValue = $this->inventoryService->getStockValue($storeId);

        $stores = Store::orderBy('store_name')->get();

        return view('inventory.stock-value', compact('stockValue', 'stores', 'storeId'));
    }

    /**
     * Get stock levels for an item (AJAX)
     */
    public function getItemStock(Request $request)
    {
        $itemId = $request->get('item_id');
        $storeId = $request->get('store_id');

        $query = InventoryStock::where('item_id', $itemId)
            ->where('quantity', '>', 0);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $stocks = $query->with(['store', 'bin', 'batch'])->get();

        return response()->json([
            'stocks' => $stocks->map(function ($stock) {
                return [
                    'store' => $stock->store->store_name,
                    'bin' => $stock->bin ? $stock->bin->name : 'No Bin',
                    'batch' => $stock->batch->batch_no,
                    'quantity' => $stock->quantity,
                    'unit_cost' => $stock->batch->unit_cost,
                ];
            }),
            'total_quantity' => $stocks->sum('quantity'),
        ]);
    }

    /**
     * Generate inventory movement report
     */
    public function movementReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $itemId = $request->get('item_id');

        // This would need to be implemented based on your audit/movement tracking
        // For now, returning placeholder data
        $movements = collect([]);

        return view('inventory.movement-report', compact(
            'movements', 'startDate', 'endDate', 'itemId'
        ));
    }

    /**
     * Adjust stock quantity (for corrections)
     */
    public function adjustStock(Request $request)
    {
        $request->validate([
            'inventory_stock_id' => 'required|exists:inventory_stock,id',
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $stock = InventoryStock::findOrFail($request->inventory_stock_id);
            $oldQuantity = $stock->quantity;
            $difference = $request->new_quantity - $oldQuantity;

            $stock->update([
                'quantity' => $request->new_quantity,
                'last_updated' => now(),
            ]);

            // Log the adjustment
            \Log::info('Stock Adjustment', [
                'item_id' => $stock->item_id,
                'store_id' => $stock->store_id,
                'batch_id' => $stock->batch_id,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $request->new_quantity,
                'difference' => $difference,
                'reason' => $request->reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock quantity adjusted successfully.',
                'new_quantity' => $request->new_quantity,
                'difference' => $difference,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get low stock count
     */
    private function getLowStockCount()
    {
        return Item::where('is_active', true)
            ->where('reorder_point', '>', 0)
            ->whereHas('inventoryStock', function ($q) {
                $q->groupBy('item_id')
                  ->havingRaw('SUM(quantity) <= (SELECT reorder_point FROM items WHERE items.id = inventory_stock.item_id)');
            })
            ->count();
    }
}