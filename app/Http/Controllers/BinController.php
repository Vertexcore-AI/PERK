<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\Store;
use Illuminate\Http\Request;

class BinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bin::with('store');

        // Filter by store if specified
        if ($request->has('store') && $request->store) {
            $query->where('store_id', $request->store);
        }

        $bins = $query->latest()->paginate(15);

        $totalBins = Bin::count();
        $totalStores = Store::count();
        $totalStockValue = 0; // Will be calculated in Phase 2

        // Get the filtered store if filtering
        $filteredStore = null;
        if ($request->has('store') && $request->store) {
            $filteredStore = Store::find($request->store);
        }

        return view('bins.index', compact('bins', 'totalBins', 'totalStores', 'totalStockValue', 'filteredStore'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $stores = Store::all();
        $selectedStoreId = $request->get('store_id');
        return view('bins.create', compact('stores', 'selectedStoreId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Bin::create($request->only(['store_id', 'code', 'name', 'description']));

        return redirect()->route('bins.index')
            ->with('success', 'Bin created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bin $bin)
    {
        $bin->load('store');
        return view('bins.show', compact('bin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bin $bin)
    {
        $stores = Store::all();
        return view('bins.edit', compact('bin', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bin $bin)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $bin->update($request->only(['store_id', 'code', 'name', 'description']));

        return redirect()->route('bins.index')
            ->with('success', 'Bin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bin $bin)
    {
        $bin->delete();

        return redirect()->route('bins.index')
            ->with('success', 'Bin deleted successfully.');
    }

    //bin and store export to csv

    public function exportCsv()
    {
        $fileName = 'bins_stores_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // 1️⃣ BIN DATA SECTION
            fputcsv($file, ['++++ BIN RELATED VALUES ++++']); // section header
            fputcsv($file, ['ID', 'Store ID', 'Code', 'Name', 'Description', 'Is Active', 'Created At', 'Updated At']);

            $bins = Bin::all();
            foreach ($bins as $bin) {
                fputcsv($file, [
                    $bin->id,
                    $bin->store_id,
                    $bin->code,
                    $bin->name,
                    $bin->description,
                    $bin->is_active,
                    $bin->created_at,
                    $bin->updated_at,
                ]);
            }

            fputcsv($file, []); // blank line for separation

            // 2️⃣ STORE DATA SECTION
            fputcsv($file, ['++++ STORE RELATED VALUES ++++']); // section header
            fputcsv($file, ['ID', 'Store Name', 'Store Location', 'Created At', 'Updated At']);

            $stores = Store::all();
            foreach ($stores as $store) {
                fputcsv($file, [
                    $store->id,
                    $store->store_name,
                    $store->store_location,
                    $store->created_at,
                    $store->updated_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
