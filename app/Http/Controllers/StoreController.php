<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::withCount('bins')
            ->latest()
            ->paginate(15);

        $totalStores = Store::count();
        $totalBins = DB::table('bins')->count();
        $totalStockValue = 0; // Will be calculated in Phase 2

        return view('stores.index', compact('stores', 'totalStores', 'totalBins', 'totalStockValue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_location' => 'nullable|string|max:255',
        ]);

        Store::create($request->only(['store_name', 'store_location']));

        return redirect()->route('stores.index')
            ->with('success', 'Store created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        $store->load('bins');
        return view('stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_location' => 'nullable|string|max:255',
        ]);

        $store->update($request->only(['store_name', 'store_location']));

        return redirect()->route('stores.index')
            ->with('success', 'Store updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }
}
