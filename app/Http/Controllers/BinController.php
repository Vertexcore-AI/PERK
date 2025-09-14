<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BinController extends Controller
{
    public function index()
    {
        $bins = Bin::paginate(15);
        return view('Bin.index', compact('bins'));
    }

    public function create()
    {
        $stores = Store::all();
        return view('Bin.create', compact('stores'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'bin_name'   => 'required|string|max:255',
            'description' => 'nullable|string',
            'store_id'   => 'required|exists:stores,store_id',
        ]);

        Bin::create($request->only(['store_id', 'bin_name', 'description']));

        return redirect()->route('bins.index')
            ->with('success', 'Bin created successfully');
    }

    public function show(Bin $bins)
    {
        return view('bins.show', compact('bins'));
    }

    public function edit(Bin $bin)
    {
        $stores = Store::all();
        return view('Bin.edit', compact('bin', 'stores'));
    }

    public function update(Request $request, Bin $bin)
    {
        $request->validate([
            'bin_name'   => 'required|string|max:255',
            'description' => 'nullable|string',
            'store_id'   => 'required|exists:stores,store_id',
        ]);

        $bin->update([
            'bin_name'   => $request->bin_name,
            'description' => $request->description,
            'store_id'   => $request->store_id,
        ]);

        return redirect()->route('bins.index')
            ->with('success', 'Bin updated successfully!');
    }

    public function exportCsv()
{
    // Fetch all bins
    $bins = Bin::all();

    // Define CSV headers
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="bins.csv"',
    ];

    // Open output stream
    $callback = function() use ($bins) {
        $file = fopen('php://output', 'w');

        // Add CSV column headers
        fputcsv($file, ['Bin ID', 'Store ID', 'Bin Name', 'Description', 'Created At']);

        // Add vendor data
        foreach ($bins as $vendor) {
            fputcsv($file, [
                $vendor->bin_id,
                $vendor->store_id,
                $vendor->bin_name,
                $vendor->description,
                $vendor->created_at->format('d/m/Y H:i')
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}
}
