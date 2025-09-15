<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Store;
use App\Models\Bin;
use App\Services\GRNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GRNController extends Controller
{
    protected $grnService;

    public function __construct(GRNService $grnService)
    {
        $this->grnService = $grnService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grns = GRN::with(['vendor', 'grnItems'])
            ->latest()
            ->paginate(15);

        $totalGRNs = GRN::count();
        $pendingGRNs = GRN::whereHas('grnItems', function ($query) {
            $query->whereColumn('stored_qty', '<', 'received_qty');
        })->count();
        $todayGRNs = GRN::whereDate('created_at', today())->count();
        $totalValue = GRN::sum('total_amount');

        return view('grns.index', compact('grns', 'totalGRNs', 'pendingGRNs', 'todayGRNs', 'totalValue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->get();
        $stores = Store::all();
        $bins = Bin::all();

        return view('grns.create', compact('vendors', 'items', 'stores', 'bins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'inv_no' => 'required|string|max:50',
            'billing_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.vendor_item_code' => 'required|string',
            'items.*.received_qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100',
            'items.*.stored_qty' => 'nullable|integer|min:0',
            'items.*.store_id' => 'required|exists:stores,id',
            'items.*.bin_id' => 'nullable|exists:bins,id',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            \Log::info('GRN Creation Started', ['request_data' => $request->all()]);
            $grn = $this->grnService->processGRN($request->all());
            \Log::info('GRN Created Successfully', ['grn_id' => $grn->grn_id]);

            return redirect()->route('grns.show', $grn->grn_id)
                ->with('success', 'GRN created successfully.');
        } catch (\Exception $e) {
            \Log::error('GRN Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating GRN: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GRN $grn)
    {
        $grn->load([
            'vendor',
            'grnItems.item.category',
            'grnItems.batch',
        ]);

        return view('grns.show', compact('grn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GRN $grn)
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->get();
        $stores = Store::all();
        $bins = Bin::all();

        $grn->load('grnItems.item');

        return view('grns.edit', compact('grn', 'vendors', 'items', 'stores', 'bins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GRN $grn)
    {
        $request->validate([
            'billing_date' => 'required|date',
        ]);

        $grn->update([
            'billing_date' => $request->billing_date,
        ]);

        return redirect()->route('grns.show', $grn->grn_id)
            ->with('success', 'GRN updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GRN $grn)
    {
        // Check if any items have been stored
        $hasStoredItems = $grn->grnItems()->where('stored_qty', '>', 0)->exists();

        if ($hasStoredItems) {
            return redirect()->back()
                ->with('error', 'Cannot delete GRN with stored items. Please reverse the stock first.');
        }

        $grn->delete();

        return redirect()->route('grns.index')
            ->with('success', 'GRN deleted successfully.');
    }

    /**
     * Update stored quantity for a GRN item
     */
    public function updateStoredQty(Request $request, $grnId, $grnItemId)
    {
        $request->validate([
            'stored_qty' => 'required|integer|min:0',
            'store_id' => 'required|exists:stores,id',
            'bin_id' => 'nullable|exists:bins,id',
        ]);

        try {
            DB::transaction(function () use ($request, $grnItemId) {
                $grnItem = GRNItem::findOrFail($grnItemId);
                
                if ($request->stored_qty > $grnItem->received_qty) {
                    throw new \Exception('Stored quantity cannot exceed received quantity.');
                }

                // Update inventory stock
                $quantityDiff = $request->stored_qty - $grnItem->stored_qty;
                
                if ($quantityDiff != 0) {
                    InventoryStock::updateStock(
                        $grnItem->item_id,
                        $request->store_id,
                        $request->bin_id,
                        $grnItem->batch_id,
                        $quantityDiff
                    );
                }

                // Update GRN item
                $grnItem->stored_qty = $request->stored_qty;
                $grnItem->save();
            });

            return response()->json(['success' => true, 'message' => 'Stored quantity updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}