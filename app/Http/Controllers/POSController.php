<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Customer;
use App\Models\Batch;
use App\Services\SalesService;
use App\Services\CustomerService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class POSController extends Controller
{
    public function __construct(
        private SalesService $salesService,
        private CustomerService $customerService,
        private InventoryService $inventoryService
    ) {}

    /**
     * Main POS interface
     */
    public function index(): View
    {
        $recentCustomers = Customer::orderBy('created_at', 'desc')->take(10)->get();

        return view('pos.index', compact('recentCustomers'));
    }

    /**
     * Search items for POS (AJAX)
     */
    public function searchItems(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 20);

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $items = Item::with(['category'])
            ->where(function ($query) use ($search) {
                $query->where('item_no', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('manufacturer_name', 'LIKE', "%{$search}%");
            })
            ->limit($limit)
            ->get()
            ->flatMap(function ($item) {
                $batches = $this->salesService->getAvailableBatchesForItem($item->id);

                // Create separate result for each batch
                return collect($batches)->map(function ($batch) use ($item) {
                    return [
                        'item_id' => $item->id,
                        'item_no' => $item->item_no,
                        'description' => $item->description,
                        'manufacturer_name' => $item->manufacturer_name,
                        'category' => $item->category?->name,
                        'available_stock' => $batch['available_quantity'],
                        'is_serialized' => $item->is_serialized,
                        'batch_id' => $batch['batch_id'],
                        'batch_number' => $batch['batch_number'],
                        'selling_price' => $batch['selling_price'],
                        'unit_cost' => $batch['unit_cost'],
                        'expiry_date' => $batch['expiry_date'],
                    ];
                });
            });

        return response()->json($items);
    }

    /**
     * Get available batches for an item (AJAX)
     */
    public function getBatches(Item $item): JsonResponse
    {
        $batches = $this->salesService->getAvailableBatchesForItem($item->id);

        return response()->json($batches);
    }

    /**
     * Preview batch selection for given quantity (AJAX)
     */
    public function previewBatchSelection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'preferred_batch_id' => 'nullable|exists:batches,batch_id'
        ]);

        $preview = $this->salesService->previewBatchSelection(
            $validated['item_id'],
            $validated['quantity'],
            $validated['preferred_batch_id'] ?? null
        );

        return response()->json($preview);
    }

    /**
     * Calculate cart total with discounts and taxes (AJAX)
     */
    public function calculateTotal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,item_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100',
        ]);

        $totals = $this->salesService->calculateSaleTotal($validated['items']);

        return response()->json($totals);
    }

    /**
     * Validate stock availability (AJAX)
     */
    public function validateStock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,item_id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $validation = $this->salesService->validateStockAvailability($validated['items']);

            return response()->json([
                'valid' => true,
                'validation' => $validation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Search customers for POS (AJAX)
     */
    public function searchCustomers(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 10);

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $customers = $this->customerService->searchCustomers($search, $limit);

        return response()->json($customers);
    }

    /**
     * Quick create customer during POS (AJAX)
     */
    public function quickCreateCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:20',
            'type' => 'nullable|in:Retail,Insurance,Wholesale,Corporate'
        ]);

        try {
            $customer = $this->customerService->createCustomer([
                'name' => $validated['name'],
                'contact' => $validated['contact'] ?? null,
                'type' => $validated['type'] ?? 'Retail'
            ]);

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process POS sale transaction
     */
    public function processSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:cash,card,mixed',
            'cash_amount' => 'required_if:payment_method,cash,mixed|numeric|min:0',
            'card_amount' => 'required_if:payment_method,card,mixed|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_id' => 'nullable|exists:batches,id',
        ]);

        try {
            // Add sale date
            $validated['sale_date'] = now()->toDateString();

            $sale = $this->salesService->processSale($validated);

            return response()->json([
                'success' => true,
                'sale_id' => $sale->sale_id,
                'total_amount' => $sale->total_amount,
                'message' => 'Sale processed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate receipt for completed sale
     */
    public function generateReceipt(Request $request): View
    {
        $saleId = $request->get('sale_id');

        $sale = \App\Models\Sale::with([
            'customer',
            'saleItems.item',
            'saleItems.batch'
        ])->findOrFail($saleId);

        return view('pos.receipt', compact('sale'));
    }

    /**
     * Get POS dashboard data (AJAX)
     */
    public function getDashboardData(): JsonResponse
    {
        $today = now()->toDateString();

        $todaySales = \App\Models\Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->get();

        $stats = [
            'today_sales_count' => $todaySales->count(),
            'today_sales_amount' => $todaySales->sum('total_amount'),
            'today_cash_sales' => $todaySales->where('payment_method', 'cash')->sum('total_amount'),
            'today_card_sales' => $todaySales->where('payment_method', 'card')->sum('total_amount'),
            'recent_sales' => $todaySales->take(5)->map(function ($sale) {
                return [
                    'sale_id' => $sale->sale_id,
                    'customer_name' => $sale->customer->name,
                    'total_amount' => $sale->total_amount,
                    'payment_method' => $sale->payment_method,
                    'created_at' => $sale->created_at->format('H:i'),
                ];
            })
        ];

        return response()->json($stats);
    }

    /**
     * Print receipt (trigger browser print)
     */
    public function printReceipt(int $saleId): View
    {
        $sale = \App\Models\Sale::with([
            'customer',
            'saleItems.item',
            'saleItems.batch'
        ])->findOrFail($saleId);

        return view('pos.print-receipt', compact('sale'));
    }
}
