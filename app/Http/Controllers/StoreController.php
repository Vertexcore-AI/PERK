<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::paginate(15);
        return view('Stores.index', compact('stores'));
    }

    public function create()
    {
        return view('Stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_location' => 'nullable|string|max:255',
        ]);

        Store::create($request->only(['store_name', 'store_location']));

        return redirect()->route('stores.index')
            ->with('success', 'store created successfully');
    }

    public function show(Store $store)
    {
        return view('store.show', compact('store'));
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_location' => 'nullable|string|max:255',
        ]);

        $store->update($request->only(['store_name', 'store_location']));

        return redirect()->route('stores.index')
            ->with('success', 'store updated successfully');
    }

    public function exportCsv()
{
    // Fetch all stores
    $stores = Store::all();

    // Define CSV headers
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="stores.csv"',
    ];

    // Open output stream
    $callback = function() use ($stores) {
        $file = fopen('php://output', 'w');

        // Add CSV column headers
        fputcsv($file, ['ID', 'Store Name', 'Store Location', 'Created At']);

        // Add vendor data
        foreach ($stores as $vendor) {
            fputcsv($file, [
                $vendor->id,
                $vendor->store_name,
                $vendor->store_location,
                $vendor->created_at->format('d/m/Y H:i')
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}
}
