<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        //Item statistics
        $totalItems = Item::count();
        $activeItems = Item::where('is_active', true)->count();
        $activePercentage = $totalItems > 0 ? round(($activeItems / $totalItems) * 100) : 0;

        //Quotation statistics
        $projectsInProgress = Quotation::count();
        $projectPercentage = $totalItems > 0 ? round(($projectsInProgress / $totalItems) * 100, 2) : 0;

        //Profit statistics
        $totalProfit = SaleItem::sum(DB::raw('total - unit_cost'));

        //Daily sales statistics
        $today = Carbon::today();

        // Total sales today
        $totalDailySales = Sale::whereDate('sale_date', $today)->sum('total_amount');

        // Average sales today (optional)
        $avgDailySales = Sale::whereDate('sale_date', $today)->avg('total_amount');
        $avgDailySales = $avgDailySales ?? 0; // handle null

        // You can also calculate percentage relative to a target, e.g., target LKR 100,000
        $dailyTarget = 100000;
        $dailySalesPercentage = $dailyTarget > 0 ? round(($totalDailySales / $dailyTarget) * 100, 2) : 0;

        //Sales Discount statistics
        $avgDiscount = Sale::avg('discount_percentage');
        $avgDiscount = $avgDiscount ?? 0; // handle null

        // Optional: total discount amount today
        $today = Carbon::today();
        $totalDiscountAmount = Sale::whereDate('sale_date', $today)->sum('discount_amount');


        return view('pages.dashboard.index', compact(
            'totalItems',
            'activeItems',
            'activePercentage',
            'projectsInProgress',
            'projectPercentage',
            'totalProfit',
            'totalDailySales',
            'avgDailySales',
            'dailySalesPercentage',
            'avgDiscount',
            'totalDiscountAmount'

        ));
    }
}
