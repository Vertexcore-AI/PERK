<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }} - PERK Enterprises</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Print Controls -->
        <div class="no-print mb-6 flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Print Quotation</h1>
                <p class="text-sm text-gray-600">Quotation #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Print
                </button>
                <a href="{{ route('quotations.show', $quotation->quote_id) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Back
                </a>
            </div>
        </div>

        <!-- Quotation Document -->
        <div class="border border-gray-300 rounded-lg p-8 bg-white">
            <!-- Header -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">QUOTATION</h1>
                    <div class="text-lg font-semibold text-blue-600">
                        #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900 mb-2">PERK ENTERPRISES</div>
                    <div class="text-sm text-gray-600">
                        Auto Parts & Accessories<br>
                        Colombo, Sri Lanka<br>
                        Phone: +94 11 234 5678<br>
                        Email: info@perkenterprises.lk
                    </div>
                </div>
            </div>

            <!-- Customer & Quote Details -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <!-- Customer Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                        QUOTE TO:
                    </h3>
                    <div class="space-y-1">
                        <div class="font-semibold text-gray-900">{{ $quotation->manual_customer_name ?: $quotation->customer->name }}</div>
                        <div class="text-sm text-gray-600">Customer Type: {{ $quotation->customer->type }}</div>
                        <div class="text-sm text-gray-600">Contact: {{ $quotation->customer->contact }}</div>
                        <div class="text-sm text-gray-600">{{ $quotation->manual_customer_address ?: $quotation->customer->address ?: 'No address provided' }}</div>
                        @if($quotation->car_model || $quotation->car_registration_number)
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                @if($quotation->car_model)
                                    <div class="text-sm text-gray-600">Vehicle: {{ $quotation->car_model }}</div>
                                @endif
                                @if($quotation->car_registration_number)
                                    <div class="text-sm text-gray-600">Registration: {{ $quotation->car_registration_number }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quote Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                        QUOTE DETAILS:
                    </h3>
                    <div class="space-y-1">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Quote Date:</span>
                            <span class="text-sm font-medium">{{ $quotation->quote_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Valid Until:</span>
                            <span class="text-sm font-medium">{{ $quotation->valid_until->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="text-sm font-medium">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $quotation->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $quotation->status === 'Converted' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $quotation->status === 'Expired' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $quotation->status === 'Rejected' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $quotation->status }}
                                </span>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Items:</span>
                            <span class="text-sm font-medium">{{ $quotation->quoteItems->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">QUOTED ITEMS</h3>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-900">#</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-900">Item Description</th>
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-900">Batch</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-900">Qty</th>
                            <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-900">Unit Price</th>
                            <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-900">Discount %</th>
                            <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-900">VAT %</th>
                            <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->quoteItems as $index => $item)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="border border-gray-300 px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 px-4 py-3">
                                <div class="font-medium text-sm text-gray-900">{{ $item->item->description }}</div>
                                <div class="text-xs text-gray-500">{{ $item->item->item_code }}</div>
                            </td>
                            <td class="border border-gray-300 px-4 py-3">
                                @if($item->batch)
                                    <div class="text-sm text-gray-900">{{ $item->batch->batch_no }}</div>
                                    @if($item->batch->expiry_date)
                                        <div class="text-xs text-gray-500">Exp: {{ \Carbon\Carbon::parse($item->batch->expiry_date)->format('M Y') }}</div>
                                    @endif
                                @else
                                    <div class="text-sm text-orange-600 font-medium">No Stock</div>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900">{{ number_format($item->quantity) }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-900">LKR {{ number_format($item->unit_price, 2) }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-900">{{ number_format($item->discount, 2) }}%</td>
                            <td class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-900">{{ number_format($item->vat, 2) }}%</td>
                            <td class="border border-gray-300 px-4 py-3 text-right text-sm font-medium text-gray-900">LKR {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="flex justify-end mb-8">
                <div class="w-80">
                    @php
                        $subtotal = $quotation->quoteItems->sum(function($item) {
                            return $item->quantity * $item->unit_price;
                        });
                        $totalDiscount = $quotation->quoteItems->sum(function($item) {
                            return ($item->quantity * $item->unit_price) * ($item->discount / 100);
                        });
                        $totalVat = $quotation->quoteItems->sum(function($item) {
                            $afterDiscount = ($item->quantity * $item->unit_price) - (($item->quantity * $item->unit_price) * ($item->discount / 100));
                            return $afterDiscount * ($item->vat / 100);
                        });
                    @endphp

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">LKR {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Discount:</span>
                            <span class="font-medium">LKR {{ number_format($totalDiscount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total VAT:</span>
                            <span class="font-medium">LKR {{ number_format($totalVat, 2) }}</span>
                        </div>
                        <hr class="border-gray-300">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900">TOTAL ESTIMATE:</span>
                            <span class="text-blue-600">LKR {{ number_format($quotation->total_estimate, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="border-t border-gray-300 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">TERMS & CONDITIONS</h3>
                <div class="text-sm text-gray-600 space-y-2">
                    <div>• This quotation is valid until {{ $quotation->valid_until->format('M d, Y') }}.</div>
                    <div>• Prices are in Sri Lankan Rupees (LKR) and are subject to change without notice.</div>
                    <div>• All parts are subject to availability at the time of order confirmation.</div>
                    <div>• Installation charges (if applicable) are not included in the quoted prices.</div>
                    <div>• Payment terms: {{ $quotation->customer->type === 'Retail' ? 'Cash on Delivery' : 'Net 30 days' }}.</div>
                    <div>• This quotation does not constitute a binding contract until a purchase order is issued.</div>
                    <div>• Warranty terms apply as per manufacturer specifications.</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-300">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">For Any Queries:</h4>
                        <div class="text-sm text-gray-600">
                            Email: sales@perkenterprises.lk<br>
                            Phone: +94 11 234 5678<br>
                            Whatsapp: +94 77 123 4567
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">
                            <div>Authorized by: PERK Enterprises</div>
                            <div class="mt-4">
                                <div class="border-b border-gray-400 w-32 ml-auto mb-1"></div>
                                <div>Signature & Date</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Information -->
            <div class="mt-6 pt-4 border-t border-gray-200 text-xs text-gray-400 text-center">
                Generated on {{ now()->format('M d, Y \a\t H:i:s') }} |
                Quotation ID: {{ $quotation->quote_id }} |
                This is a computer-generated document
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads if print parameter is present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.onload = function() {
                window.print();
            }
        }
    </script>
</body>
</html>