<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Batch;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quotations = Quotation::with(['customer', 'quoteItems'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        // Get ALL items, including those without stock
        $items = Item::with(['batches'])->orderBy('description')->get();

        // Separate items with and without stock for frontend
        $items = $items->map(function ($item) {
            $availableBatches = $item->batches->filter(function ($batch) {
                return $batch->remaining_qty > 0;
            });

            $item->has_stock = $availableBatches->count() > 0;
            $item->available_batches = $availableBatches;
            $item->all_batches = $item->batches;

            return $item;
        });

        return view('quotations.create', compact('customers', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log all request data
        \Log::info('Quotation store request data:', [
            'all_data' => $request->all(),
            'items' => $request->input('items'),
            'customer_id' => $request->input('customer_id'),
            'validity_days' => $request->input('validity_days')
        ]);

        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'validity_days' => 'nullable|integer|min:1|max:365',
                'car_model' => 'nullable|string|max:255',
                'car_registration_number' => 'nullable|string|max:50',
                'manual_customer_name' => 'required|string|max:255',
                'manual_customer_address' => 'nullable|string|max:500',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.batch_id' => 'nullable|exists:batches,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0|max:100',
                'items.*.vat' => 'nullable|numeric|min:0|max:100'
            ]);

            \Log::info('Validation passed, validated data:', $validated);

            $quotation = $this->quotationService->createQuotation($validated);

            \Log::info('Quotation created successfully:', ['quote_id' => $quotation->quote_id]);

            return redirect()->route('quotations.show', $quotation->quote_id)
                ->with('success', 'Quotation created successfully.')
                ->with('download_pdf', true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Quotation creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quotation = Quotation::with(['customer', 'quoteItems.item', 'quoteItems.batch'])
            ->findOrFail($id);

        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quotation = Quotation::with(['quoteItems.item', 'quoteItems.batch'])
            ->findOrFail($id);

        if ($quotation->status !== 'Pending') {
            return redirect()->route('quotations.show', $id)
                ->with('error', 'Cannot edit a ' . strtolower($quotation->status) . ' quotation.');
        }

        $customers = Customer::orderBy('name')->get();
        // Get ALL items, including those without stock
        $items = Item::with(['batches'])->orderBy('description')->get();

        // Separate items with and without stock for frontend
        $items = $items->map(function ($item) {
            $availableBatches = $item->batches->filter(function ($batch) {
                return $batch->remaining_qty > 0;
            });

            $item->has_stock = $availableBatches->count() > 0;
            $item->available_batches = $availableBatches;
            $item->all_batches = $item->batches;

            return $item;
        });

        return view('quotations.edit', compact('quotation', 'customers', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quotation = Quotation::findOrFail($id);

        if ($quotation->status !== 'Pending') {
            return redirect()->route('quotations.show', $id)
                ->with('error', 'Cannot update a ' . strtolower($quotation->status) . ' quotation.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'validity_days' => 'nullable|integer|min:1|max:365',
            'car_model' => 'nullable|string|max:255',
            'car_registration_number' => 'nullable|string|max:50',
            'manual_customer_name' => 'nullable|string|max:255',
            'manual_customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_id' => 'nullable|exists:batches,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100'
        ]);

        try {
            DB::transaction(function () use ($quotation, $validated) {
                // Delete existing items
                $quotation->quoteItems()->delete();

                // Update quotation
                $quotation->update([
                    'customer_id' => $validated['customer_id'],
                    'valid_until' => now()->addDays((int)($validated['validity_days'] ?? 30))->toDateString(),
                    'car_model' => $validated['car_model'] ?? null,
                    'car_registration_number' => $validated['car_registration_number'] ?? null,
                    'manual_customer_name' => $validated['manual_customer_name'] ?? null,
                    'manual_customer_address' => $validated['manual_customer_address'] ?? null
                ]);

                // Create new items
                $totalEstimate = 0;
                foreach ($validated['items'] as $itemData) {
                    $total = $this->calculateItemTotal($itemData);
                    $quotation->quoteItems()->create([
                        'item_id' => $itemData['item_id'],
                        'batch_id' => $itemData['batch_id'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'discount' => $itemData['discount'] ?? 0,
                        'vat' => $itemData['vat'] ?? 0,
                        'total' => $total
                    ]);
                    $totalEstimate += $total;
                }

                $quotation->update(['total_estimate' => $totalEstimate]);
            });

            return redirect()->route('quotations.show', $id)
                ->with('success', 'Quotation updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quotation = Quotation::findOrFail($id);

        if ($quotation->status === 'Converted') {
            return back()->with('error', 'Cannot delete a converted quotation.');
        }

        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    /**
     * Convert quotation to sale
     */
    public function convert(Request $request, string $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_id' => 'required|exists:batches,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100'
        ]);

        try {
            $sale = $this->quotationService->convertToSale($id, $validated['items']);
            return response()->json([
                'success' => true,
                'sale_id' => $sale->sale_id,
                'message' => 'Quotation converted to sale successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Duplicate quotation
     */
    public function duplicate(string $id)
    {
        try {
            $newQuotation = $this->quotationService->duplicateQuotation($id);
            return redirect()->route('quotations.edit', $newQuotation->quote_id)
                ->with('success', 'Quotation duplicated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print quotation
     */
    public function print(string $id)
    {
        $quotation = Quotation::with(['customer', 'quoteItems.item', 'quoteItems.batch'])
            ->findOrFail($id);

        return view('quotations.print', compact('quotation'));
    }

    /**
     * API: Get pending quotations
     */
    public function getPending()
    {
        $quotations = $this->quotationService->getPendingQuotations();
        return response()->json($quotations);
    }

    /**
     * API: Check stock for quotation items
     */
    public function checkStock(string $id)
    {
        try {
            $data = $this->quotationService->loadQuotationForPOS($id);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Download PDF invoice for quotation
     */
    public function downloadPdf(string $id)
    {
        $quotation = Quotation::with(['customer', 'quoteItems.item', 'quoteItems.batch'])
            ->findOrFail($id);

        $pdf = PDF::loadView('quotations.pdf.invoice', compact('quotation'));

        $filename = 'quotation_' . str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Helper method to calculate item total
     */
    private function calculateItemTotal(array $itemData): float
    {
        $subtotal = $itemData['quantity'] * $itemData['unit_price'];
        $afterDiscount = $subtotal - ($subtotal * ($itemData['discount'] ?? 0) / 100);
        $withVat = $afterDiscount + ($afterDiscount * ($itemData['vat'] ?? 0) / 100);
        return round($withVat, 2);
    }
}
