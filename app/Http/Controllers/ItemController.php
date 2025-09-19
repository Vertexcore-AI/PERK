<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with('category')
            ->latest()
            ->paginate(15);

        $categories = Category::all();

        $totalItems = Item::count();
        $activeItems = Item::where('is_active', true)->count();
        $lowStockItems = Item::whereRaw('reorder_point > COALESCE(min_stock, 0)')->count();
        // Calculate total value from batches instead of item unit_cost
        $totalValue = \DB::table('batches')
            ->join('items', 'batches.item_id', '=', 'items.id')
            ->where('items.is_active', true)
            ->where('batches.remaining_qty', '>', 0)
            ->sum(\DB::raw('batches.unit_cost * batches.remaining_qty'));

        return view('items.index', compact('items', 'categories', 'totalItems', 'activeItems', 'lowStockItems', 'totalValue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_no' => 'required|string|unique:items|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit_of_measure' => 'nullable|string|max:10',
            'reorder_point' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:255',
            'is_serialized' => 'boolean',
            'is_active' => 'boolean',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item->load(['category', 'batches.vendor']);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_no' => 'required|string|max:50|unique:items,item_no,' . $item->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit_of_measure' => 'nullable|string|max:10',
            'reorder_point' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:255',
            'is_serialized' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }

    //item csv export

    public function exportCsv()
    {
        $fileName = 'items_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = [
            'ID',
            'Item No',
            'Name',
            'Description',
            'Category ID',
            'Unit of Measure',
            'Reorder Point',
            'Barcode',
            'Min Stock',
            'Max Stock',
            'Manufacturer Name',
            'Is Serialized',
            'Is Active',
            'Created At',
            'Updated At'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');

            // CSV header row
            fputcsv($file, $columns);

            // Fetch items
            $items = Item::all();

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->item_no,
                    $item->name,
                    $item->description,
                    $item->category_id,
                    $item->unit_of_measure,
                    $item->reorder_point,
                    $item->barcode,
                    $item->min_stock,
                    $item->max_stock,
                    $item->manufacturer_name,
                    $item->is_serialized ? 'Yes' : 'No',
                    $item->is_active ? 'Active' : 'Inactive',
                    $item->created_at,
                    $item->updated_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
