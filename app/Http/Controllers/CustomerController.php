<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('Customers.index', compact('customers'));
    }

    public function create()
    {
        return view('Customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'nullable|string|max:255'
        ]);

        Customer::create($request->only(['name', 'contact', 'address', 'type']));

        return redirect()->route('customers.index')
            ->with('success', 'customer created successfully');
    }

    public function show(Customer $customer)
    {
        return view('Customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('Customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'nullable|string|max:255'
        ]);

        $customer->update($request->only(['name', 'contact', 'address', 'type']));

        return redirect()->route('customers.index')
            ->with('success', 'customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'customer deleted successfully');
    }

    public function exportCsv()
    {
        // Fetch all customers
        $customers = Customer::all();

        // CSV headers for the HTTP response
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers.csv"',
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');

            // CSV column headers
            fputcsv($file, [
                'ID',
                'Name',
                'Contact',
                'Address',
                'Type',
                'Created At',
            ]);

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->customer_id,
                    $customer->name,
                    $customer->contact ?? '',
                    $customer->address ?? '',
                    $customer->type,
                    $customer->created_at ? Carbon::parse($customer->created_at)->format('d/m/Y H:i') : '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
