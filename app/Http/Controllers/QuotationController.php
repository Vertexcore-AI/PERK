<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('customer')->paginate(15);
        return view('Quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('Quotations.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,customer_id',
            'quote_date'    => 'required|date',
            'valid_until'   => 'required|date|after_or_equal:quote_date',
            'total_estimate' => 'required|numeric|min:0',
            'status'        => 'required|string|in:Pending,Approved,Rejected',
        ]);

        Quotation::create($request->only([
            'customer_id',
            'quote_date',
            'valid_until',
            'total_estimate',
            'status',
        ]));

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully');
    }

    public function show(Quotation $quotation)
    {
        return view('quotations,show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::all();
        return view('quotations.edit', compact('quotation', 'customers'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,customer_id',
            'quote_date'     => 'required|date',
            'valid_until'    => 'required|date|after_or_equal:quote_date',
            'total_estimate' => 'required|numeric|min:0',
            'status'         => 'required|string|in:Pending,Approved,Rejected',
        ]);

        $quotation->update($request->only([
            'customer_id',
            'quote_date',
            'valid_until',
            'total_estimate',
            'status',
        ]));

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation updated successfully');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully');
    }

    public function exportCsv()
    {
        // Fetch all quotations with customer relationship
        $quotations = Quotation::with('customer')->get();

        // CSV headers for the HTTP response
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="quotations.csv"',
        ];

        $callback = function () use ($quotations) {
            $file = fopen('php://output', 'w');

            // CSV column headers
            fputcsv($file, [
                'ID',
                'Customer Name',
                'Quote Date',
                'Valid Until',
                'Total Estimate',
                'Status',
                'Created At'
            ]);

            foreach ($quotations as $quote) {
                fputcsv($file, [
                    $quote->quote_id,
                    $quote->customer->name ?? 'N/A', // safe if customer is missing
                    $quote->quote_date ? Carbon::parse($quote->quote_date)->format('d/m/Y') : '',
                    $quote->valid_until ? Carbon::parse($quote->valid_until)->format('d/m/Y') : '',
                    number_format($quote->total_estimate, 2),
                    $quote->status,
                    $quote->created_at ? $quote->created_at->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
