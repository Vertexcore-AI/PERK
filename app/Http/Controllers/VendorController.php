<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::paginate(15);
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string'
        ]);

        Vendor::create($request->only(['name', 'contact', 'address']));

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully');
    }

    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string'
        ]);

        $vendor->update($request->only(['name', 'contact', 'address']));

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor updated successfully');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully');
    }

    //export vendors
    public function exportCsv()
    {
        // Fetch all vendors
        $vendors = Vendor::all();

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendors.csv"',
        ];

        // Open output stream
        $callback = function () use ($vendors) {
            $file = fopen('php://output', 'w');

            // Add CSV column headers
            fputcsv($file, ['ID', 'Name', 'Contact', 'Address', 'Created At']);

            // Add vendor data
            foreach ($vendors as $vendor) {
                fputcsv($file, [
                    $vendor->id,
                    $vendor->name,
                    $vendor->contact,
                    $vendor->address,
                    $vendor->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
