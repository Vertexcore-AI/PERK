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
        $serializedItems = Item::where('is_serialized', true)->count();
        $lowStockItems = Item::whereRaw('COALESCE(min_stock, 0) > 0')->count();
        $totalValue = 0; // Will be calculated in Phase 2

        return view('items.index', compact('items', 'categories', 'totalItems', 'serializedItems', 'lowStockItems', 'totalValue'));
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
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'vat' => 'nullable|numeric|min:0|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'unit_of_measure' => 'nullable|string|max:10',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'is_serialized' => 'boolean',
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
        $item->load('category');
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
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'vat' => 'nullable|numeric|min:0|max:100',
            'manufacturer_name' => 'nullable|string|max:255',
            'unit_of_measure' => 'nullable|string|max:10',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'is_serialized' => 'boolean',
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
}
