<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update stock quantity for an item
     */
    public function updateStock($itemId, $storeId, $binId, $batchId, $quantity)
    {
        return InventoryStock::updateStock($itemId, $storeId, $binId, $batchId, $quantity);
    }

    /**
     * Check if stock is available for an item
     */
    public function checkStockAvailability($itemId, $quantity)
    {
        $totalStock = InventoryStock::getTotalStock($itemId);
        return $totalStock >= $quantity;
    }

    /**
     * Get stock levels by item
     */
    public function getStockByItem($itemId)
    {
        return InventoryStock::getStockByItem($itemId);
    }

    /**
     * Transfer stock between locations
     */
    public function transferStock($fromStoreId, $toStoreId, $itemId, $quantity, $fromBinId = null, $toBinId = null)
    {
        return DB::transaction(function () use ($fromStoreId, $toStoreId, $itemId, $quantity, $fromBinId, $toBinId) {
            // Get source stock records
            $sourceStocks = InventoryStock::where('item_id', $itemId)
                ->where('store_id', $fromStoreId)
                ->when($fromBinId, function ($query) use ($fromBinId) {
                    return $query->where('bin_id', $fromBinId);
                })
                ->where('quantity', '>', 0)
                ->orderBy('created_at', 'asc') // FIFO
                ->get();

            $remainingQty = $quantity;
            
            foreach ($sourceStocks as $stock) {
                if ($remainingQty <= 0) break;
                
                $transferQty = min($stock->quantity, $remainingQty);
                
                // Reduce from source
                $stock->quantity -= $transferQty;
                $stock->last_updated = now();
                $stock->save();
                
                // Add to destination
                InventoryStock::updateStock(
                    $itemId,
                    $toStoreId,
                    $toBinId,
                    $stock->batch_id,
                    $transferQty
                );
                
                $remainingQty -= $transferQty;
            }
            
            if ($remainingQty > 0) {
                throw new \Exception("Insufficient stock for transfer. Short by: {$remainingQty}");
            }
            
            return true;
        });
    }

    /**
     * Generate low stock alert report
     */
    public function generateLowStockAlert()
    {
        $items = Item::where('is_active', true)
            ->where('reorder_point', '>', 0)
            ->get();

        $lowStockItems = [];

        foreach ($items as $item) {
            $totalStock = InventoryStock::getTotalStock($item->id);
            
            if ($totalStock <= $item->reorder_point) {
                $lowStockItems[] = [
                    'item' => $item,
                    'current_stock' => $totalStock,
                    'reorder_point' => $item->reorder_point,
                    'shortage' => $item->reorder_point - $totalStock,
                ];
            }
        }

        return $lowStockItems;
    }

    /**
     * Get stock value report
     */
    public function getStockValue($storeId = null)
    {
        $query = InventoryStock::with(['item', 'batch'])
            ->where('quantity', '>', 0);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $stocks = $query->get();
        
        $totalValue = 0;
        $itemValues = [];

        foreach ($stocks as $stock) {
            $value = $stock->quantity * ($stock->batch->cost ?? $stock->item->unit_cost ?? 0);
            $totalValue += $value;
            
            $itemId = $stock->item_id;
            if (!isset($itemValues[$itemId])) {
                $itemValues[$itemId] = [
                    'item' => $stock->item,
                    'total_quantity' => 0,
                    'total_value' => 0,
                ];
            }
            
            $itemValues[$itemId]['total_quantity'] += $stock->quantity;
            $itemValues[$itemId]['total_value'] += $value;
        }

        return [
            'total_value' => $totalValue,
            'items' => array_values($itemValues),
        ];
    }

    /**
     * Deduct stock for sales (FIFO)
     */
    public function deductStock($itemId, $quantity, $storeId = null)
    {
        return DB::transaction(function () use ($itemId, $quantity, $storeId) {
            $query = InventoryStock::where('item_id', $itemId)
                ->where('quantity', '>', 0)
                ->orderBy('created_at', 'asc'); // FIFO

            if ($storeId) {
                $query->where('store_id', $storeId);
            }

            $stocks = $query->get();

            $remainingQty = $quantity;
            $deductedBatches = [];

            foreach ($stocks as $stock) {
                if ($remainingQty <= 0) break;

                $deductQty = min($stock->quantity, $remainingQty);

                $stock->quantity -= $deductQty;
                $stock->last_updated = now();
                $stock->save();

                $deductedBatches[] = [
                    'batch_id' => $stock->batch_id,
                    'quantity' => $deductQty,
                    'unit_cost' => $stock->batch->cost ?? 0,
                ];

                $remainingQty -= $deductQty;
            }

            if ($remainingQty > 0) {
                throw new \Exception("Insufficient stock. Short by: {$remainingQty}");
            }

            return $deductedBatches;
        });
    }

    /**
     * Get total available stock for an item
     */
    public function getTotalAvailableStock($itemId)
    {
        return InventoryStock::where('item_id', $itemId)
            ->where('quantity', '>', 0)
            ->sum('quantity');
    }

    /**
     * Deduct stock from specific batch
     */
    public function deductStockFromBatch($batchId, $quantity)
    {
        return DB::transaction(function () use ($batchId, $quantity) {
            $stocks = InventoryStock::where('batch_id', $batchId)
                ->where('quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $remainingQty = $quantity;

            foreach ($stocks as $stock) {
                if ($remainingQty <= 0) break;

                $deductQty = min($stock->quantity, $remainingQty);

                $stock->quantity -= $deductQty;
                $stock->last_updated = now();
                $stock->save();

                $remainingQty -= $deductQty;
            }

            if ($remainingQty > 0) {
                throw new \Exception("Insufficient stock in batch. Short by: {$remainingQty}");
            }

            return true;
        });
    }
}