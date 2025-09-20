<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

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

    //vendor export function
        public function exportCsv()
        {
            $fileName = 'vendors_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $columns = ['ID', 'Name', 'Contact', 'Address', 'Date Added'];

            $callback = function () use ($columns) {
                $file = fopen('php://output', 'w');

                // Add CSV header
                fputcsv($file, $columns);

                // Fetch all vendors
                $vendors = Vendor::all();

                foreach ($vendors as $vendor) {
                    fputcsv($file, [
                        $vendor->id,
                        $vendor->name,
                        $vendor->contact,
                        $vendor->address,
                        $vendor->date_add,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
}
