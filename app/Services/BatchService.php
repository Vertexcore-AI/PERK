<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\SerialItem;
use Illuminate\Support\Str;

class BatchService
{
    /**
     * Create a new batch for an item with actual purchase cost
     */
    public function createBatch($itemId, $vendorId, $actualCost, $quantity, $grnData = [])
    {
        $batchNumber = Batch::generateBatchNumber($itemId, $vendorId);

        return Batch::create([
            'item_id' => $itemId,
            'vendor_id' => $vendorId,
            'batch_no' => $batchNumber,
            'unit_cost' => $actualCost, // This is the actual purchase cost after discount
            'received_qty' => $quantity,
            'remaining_qty' => $quantity,
            'received_date' => now()->toDateString(),
            'expiry_date' => $grnData['expiry_date'] ?? null,
            'discount_percent' => $grnData['discount'] ?? 0,
            'vat_percent' => $grnData['vat'] ?? 0,
        ]);
    }

    /**
     * Create batch with enhanced pricing data
     */
    public function createBatchWithPricing($itemId, $vendorId, $pricingData, $quantity)
    {
        // Calculate actual cost from GRN pricing
        $unitPrice = $pricingData['unit_price'];
        $discount = $pricingData['discount'] ?? 0;
        $actualCost = $unitPrice - ($unitPrice * $discount / 100);

        $batch = $this->createBatch($itemId, $vendorId, $actualCost, $quantity, [
            'expiry_date' => $pricingData['expiry_date'] ?? null,
            'notes' => $pricingData['notes'] ?? null,
            'discount' => $pricingData['discount'] ?? 0,
            'vat' => $pricingData['vat'] ?? 0,
        ]);

        // Update vendor item mapping with latest cost if this is preferred vendor
        $this->updateVendorMappingCost($vendorId, $itemId, $actualCost);

        return $batch;
    }

    /**
     * Update vendor mapping with latest actual cost
     */
    private function updateVendorMappingCost($vendorId, $itemId, $actualCost)
    {
        $mapping = \App\Models\VendorItemMapping::where('vendor_id', $vendorId)
            ->where('item_id', $itemId)
            ->first();

        if ($mapping && $mapping->is_preferred) {
            // Update reference cost with actual cost for preferred vendors
            $mapping->update(['vendor_cost' => $actualCost]);
        }
    }

    /**
     * Update batch quantity
     */
    public function updateBatchQuantity($batchId, $quantity)
    {
        $batch = Batch::findOrFail($batchId);
        $batch->quantity += $quantity;
        $batch->save();
        
        return $batch;
    }

    /**
     * Get batches for an item (FIFO order)
     */
    public function getBatchesForItem($itemId, $onlyAvailable = true)
    {
        $query = Batch::where('item_id', $itemId)
            ->orderBy('created_at', 'asc'); // FIFO
        
        if ($onlyAvailable) {
            $query->where('quantity', '>', 0);
        }
        
        return $query->get();
    }

    /**
     * Generate serial numbers for a batch
     */
    public function generateSerialNumbers($batchId, $quantity, $prefix = null)
    {
        $batch = Batch::findOrFail($batchId);
        $serialNumbers = [];
        
        for ($i = 0; $i < $quantity; $i++) {
            $serial = $this->generateSerialNumber($batch, $prefix);
            
            $serialNumbers[] = SerialItem::create([
                'batch_id' => $batchId,
                'serial_no' => $serial,
                'status' => 0, // Available
            ]);
        }
        
        return $serialNumbers;
    }

    /**
     * Generate a unique serial number
     */
    private function generateSerialNumber($batch, $prefix = null)
    {
        if (!$prefix) {
            $prefix = 'SN';
        }
        
        $date = now()->format('YmdHis');
        $random = strtoupper(Str::random(6));
        
        return "{$prefix}-{$batch->item_id}-{$date}-{$random}";
    }

    /**
     * Update serial item status
     */
    public function updateSerialStatus($serialNo, $status)
    {
        $serial = SerialItem::where('serial_no', $serialNo)->firstOrFail();
        $serial->status = $status;
        $serial->save();
        
        return $serial;
    }

    /**
     * Get available serial numbers for a batch
     */
    public function getAvailableSerials($batchId)
    {
        return SerialItem::where('batch_id', $batchId)
            ->where('status', 0)
            ->get();
    }

    /**
     * Calculate batch cost for quantity
     */
    public function calculateBatchCost($batchId, $quantity)
    {
        $batch = Batch::findOrFail($batchId);
        return $batch->cost * $quantity;
    }

    /**
     * Get batch by batch number
     */
    public function getBatchByNumber($batchNumber)
    {
        return Batch::where('batch_number', $batchNumber)->first();
    }
}