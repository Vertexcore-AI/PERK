<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Batch;
use App\Models\Quotation;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(): View
    {
        return view('pages.dashboard.index');
    }

    /**
     * Get dashboard data (AJAX)
     */
    public function getDashboardData(): JsonResponse
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Total Daily Sales
        $todaySales = Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $yesterdaySales = Sale::whereDate('sale_date', $yesterday)
            ->where('status', 'completed')
            ->sum('total_amount');

        $salesChange = $yesterdaySales > 0
            ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100
            : 0;

        // 2. Total Inventory Value
        $inventoryValue = Batch::where('remaining_qty', '>', 0)
            ->sum(DB::raw('remaining_qty * unit_cost'));
        $totalItems = Batch::where('remaining_qty', '>', 0)->count();

        // If no batches, try to get from items table as fallback
        if ($inventoryValue == 0) {
            $inventoryValue = Batch::sum(DB::raw('received_qty * unit_cost'));
            $totalItems = Batch::count();
        }

        // 3. Quotations Count - try different status values
        $activeQuotations = Quotation::whereIn('status', ['pending', 'draft', 'sent', 'active'])->count();
        $totalQuotations = Quotation::count();

        // If no quotations with those statuses, count all as a fallback
        if ($activeQuotations == 0 && $totalQuotations > 0) {
            $activeQuotations = $totalQuotations;
        }

        $quotationConversionRate = $totalQuotations > 0
            ? ($activeQuotations / $totalQuotations) * 100
            : 0;

        // 4. Daily Profit
        $todaysSaleItems = SaleItem::whereHas('sale', function($query) use ($today) {
            $query->whereDate('sale_date', $today)
                  ->where('status', 'completed');
        })->with(['batch', 'sale'])->get();

        $todayRevenue = $todaysSaleItems->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });

        $todayCosts = $todaysSaleItems->sum(function($item) {
            // Try to get cost from batch, fallback to a reasonable estimate
            $unitCost = $item->batch->unit_cost ?? ($item->unit_price * 0.7); // 70% of selling price as fallback
            return $item->quantity * $unitCost;
        });

        // If no sale items today, use sales total as revenue and estimate costs
        if ($todayRevenue == 0 && $todaySales > 0) {
            $todayRevenue = $todaySales;
            $todayCosts = $todaySales * 0.7; // Estimate 70% cost ratio
        }

        $dailyProfit = $todayRevenue - $todayCosts;
        $profitMargin = $todayRevenue > 0 ? ($dailyProfit / $todayRevenue) * 100 : 0;

        // Recent sales for reference
        $recentSales = Sale::with('customer')
            ->whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'sale_id' => $sale->sale_id,
                    'customer_name' => $sale->customer->name ?? 'Guest',
                    'total_amount' => $sale->total_amount,
                    'payment_method' => $sale->payment_method,
                    'created_at' => $sale->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'daily_sales' => [
                'amount' => $todaySales,
                'change_percentage' => round($salesChange, 1),
                'target_percentage' => min(($todaySales / 10000) * 100, 100), // Assuming $10k daily target
            ],
            'inventory_value' => [
                'total_value' => round($inventoryValue, 2),
                'total_items' => $totalItems,
                'turnover_percentage' => 75, // Could be calculated based on sales vs inventory
            ],
            'quotations' => [
                'active_count' => $activeQuotations,
                'total_count' => $totalQuotations,
                'conversion_rate' => round($quotationConversionRate, 1),
            ],
            'daily_profit' => [
                'amount' => round($dailyProfit, 2),
                'margin_percentage' => round($profitMargin, 1),
                'target_percentage' => min(($dailyProfit / 2000) * 100, 100), // Assuming $2k daily profit target
            ],
            'recent_sales' => $recentSales,
            'updated_at' => now()->format('H:i:s'),
            // Debug info
            'debug' => [
                'batches_count' => Batch::count(),
                'batches_with_qty' => Batch::where('remaining_qty', '>', 0)->count(),
                'quotations_all_count' => Quotation::count(),
                'sale_items_today' => $todaysSaleItems->count(),
                'today_revenue' => $todayRevenue,
                'today_costs' => $todayCosts,
            ]
        ]);
    }

    /**
     * Get weekly dashboard summary
     */
    public function getWeeklySummary(): JsonResponse
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $weeklySales = Sale::whereBetween('sale_date', [$startOfWeek, $endOfWeek])
            ->where('status', 'completed')
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as daily_total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'weekly_sales' => $weeklySales,
            'week_total' => $weeklySales->sum('daily_total'),
            'week_start' => $startOfWeek->format('M d'),
            'week_end' => $endOfWeek->format('M d')
        ]);
    }
}