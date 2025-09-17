<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Services\SalesService;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function __construct(
        private SalesService $salesService,
        private CustomerService $customerService
    ) {}

    /**
     * Display sales listing with filters
     */
    public function index(Request $request): View
    {
        $query = Sale::with(['customer', 'saleItems']);

        // Apply filters
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by sale ID or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->orderBy('sale_date', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        // Get summary statistics
        $stats = $this->getSalesSummary($request);

        $customers = Customer::orderBy('name')->get();

        return view('sales.index', compact('sales', 'stats', 'customers'));
    }

    /**
     * Show sale details
     */
    public function show(Sale $sale): View
    {
        $sale->load([
            'customer',
            'saleItems.item',
            'saleItems.batch'
        ]);

        $customerStats = $this->customerService->getCustomerStats($sale->customer_id);

        return view('sales.show', compact('sale', 'customerStats'));
    }

    /**
     * Show manual sale creation form
     */
    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('customers'));
    }

    /**
     * Store a new sale (alternative to POS)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,mixed',
            'cash_amount' => 'required_if:payment_method,cash,mixed|numeric|min:0',
            'card_amount' => 'required_if:payment_method,card,mixed|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $sale = $this->salesService->processSale($validated);

            return redirect()
                ->route('sales.show', $sale->sale_id)
                ->with('success', 'Sale created successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel/delete a sale (if allowed)
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        // Only allow cancellation of recent sales (within 24 hours)
        if ($sale->created_at->diffInHours(now()) > 24) {
            return back()->withErrors(['error' => 'Cannot cancel sales older than 24 hours.']);
        }

        // TODO: Implement sale cancellation logic
        // - Restore stock to batches
        // - Update serial items status
        // - Log cancellation

        $sale->update(['status' => 'cancelled']);

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sale cancelled successfully.');
    }

    /**
     * Get sales summary statistics
     */
    private function getSalesSummary(Request $request): array
    {
        $query = Sale::query();

        // Apply same filters as main query
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->get();

        return [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'average_sale' => $sales->avg('total_amount'),
            'cash_sales' => $sales->where('payment_method', 'cash')->sum('total_amount'),
            'card_sales' => $sales->where('payment_method', 'card')->sum('total_amount'),
            'mixed_sales' => $sales->where('payment_method', 'mixed')->sum('total_amount'),
        ];
    }

    /**
     * Export sales to CSV
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV export functionality
        return response()->json(['message' => 'Export functionality coming soon']);
    }

    /**
     * Generate receipt for a sale
     */
    public function receipt(Sale $sale): View
    {
        $sale->load([
            'customer',
            'saleItems.item',
            'saleItems.batch'
        ]);

        return view('sales.receipt', compact('sale'));
    }
}
