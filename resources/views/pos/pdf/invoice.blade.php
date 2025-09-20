<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $type === 'vat' ? 'VAT INVOICE' : 'INVOICE' }} #{{ str_pad($sale->sale_id, 4, '0', STR_PAD_LEFT) }} - Perk Enterprises</title>
    <style>
        @page {
            size: letter;
            margin: 0.2in;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .container {
            width: 100%;
            position: relative;
        }

        /* Header Styles */
        .header {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 15px;
        }

        .header-row {
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 200px;
            vertical-align: top;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        .company-info {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            padding-top: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 10px;
            line-height: 1.3;
            font -color: #000;
            text-align: left;
        }

        /* Invoice Type Header */
        .invoice-type {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 5px 0;
            letter-spacing: 2px;
            padding: 5px 0;
        }

        /* Customer & Invoice Details */
        .details-section {
            margin-bottom: 10px;
        }

        .details-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .detail-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .detail-column:last-child {
            padding-right: 0;
            text-align: right;
        }

        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #000;
        }

        .detail-value {
            display: inline-block;
            font-weight: normal;
            color: #000;
        }

        .detail-item {
            margin-bottom: 5px;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            vertical-align: middle;
            color: #000;
        }

        .items-table td {
            padding: 6px 5px;
            font-size: 10px;
            vertical-align: top;
            color: #000;
        }

        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }

        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 1px solid #000;
        }


        .item-name {
            font-weight: bold;
            font-size: 10px;
        }

        .item-code {
            font-size: 9px;
            color: #000;
        }

        .batch-info {
            font-size: 9px;
            color: #000;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 10px;
            display: table;
            width: 100%;
        }

        .summary-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .summary-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .summary-row {
            margin-bottom: 5px;
        }

        .summary-label {
            display: inline-block;
            width: 150px;
            text-align: right;
            padding-right: 10px;
            color: #000;
        }

        .summary-value {
            display: inline-block;
            width: 100px;
            text-align: right;
            font-weight: bold;
            color: #000;
        }

        .total-row {
            border-top: 1px solid #000;
            padding: 8px 0;
            margin-top: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        /* Payment Section */
        .payment-section {
            margin-top: 15px;
            padding: 10px;
        }

        .payment-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .payment-row {
            margin-bottom: 4px;
            color: #000;
        }

        /* Terms Section */
        .terms-section {
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #000;
        }

        .terms-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .terms-list {
            list-style: none;
            padding-left: 10px;
        }

        .terms-list li {
            font-size: 9px;
            margin-bottom: 4px;
        }

        .terms-list li:before {
            content: "* ";
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: bottom;
        }

        .signature-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin-top: 50px;
            display: inline-block;
        }

        .signature-text {
            font-size: 10px;
            margin-top: 5px;
        }

        .thank-you {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin-top: 20px;
        }

        /* Print specific */
        @media print {
            body {
                background: white;
            }

            .container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-row">
                <!-- Left: PERK Logo & Contact -->
                <div class="logo-section">
                    @if(file_exists(public_path('images/logo.jpeg')))
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Perk Logo" class="logo">
                    @else
                        <div style="width: 100px; height: 50px; background: #e91e63; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px;">
                            PERK
                        </div>
                    @endif
                    <div class="perk-contact">
                        <div><strong>No. 174/B, Katugastota Road, Kandy</strong></div>
                        <div>Tel: 0771058905 | 0817390070</div>
                    </div>
                </div>

                <!-- Right: Ideal Motors Info -->
                <div class="company-info" style="text-align: right; margin-top: -150px;">
                    @if(file_exists(public_path('images/IDEAL-MOTORS.jpg')))
                        <img src="{{ asset('images/IDEAL-MOTORS.jpg') }}" alt="Ideal Motors" style="height: 40px; width: auto; margin-bottom: 5px;">
                    @endif

                    <div class="ideal-info">
                        <div class="company-tagline"><strong>Authorized Dealer IDEAL Motors Pvt Ltd</strong></div>
                        <div class="ideal-contact">
                            <div>No.299, Union Place Colombo 02, Sri Lanka</div>
                            <div>Hot Line: 0117755555</div>
                            <div>Email: spareparts@idealgroup.lk</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Type -->
        <div class="invoice-type">
            {{ $type === 'vat' ? 'VAT INVOICE' : 'INVOICE' }}
        </div>

        <!-- Customer & Invoice Details -->
        <div class="details-section">
            <div class="details-row">
                <div class="detail-column">
                    <div class="detail-item">
                        <span class="detail-label">Invoice No:</span>
                        <span class="detail-value">#{{ str_pad($sale->sale_id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ $sale->sale_date->format('F d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Time:</span>
                        <span class="detail-value">{{ $sale->created_at->format('h:i A') }}</span>
                    </div>
                </div>

                <div class="detail-column">
                    <div class="detail-item">
                        <span class="detail-label">Customer:</span>
                        <span class="detail-value">{{ $sale->customer->name }}</span>
                    </div>
                    @if($sale->customer->company)
                        <div class="detail-item">
                            <span class="detail-label">Company:</span>
                            <span class="detail-value">{{ $sale->customer->company }}</span>
                        </div>
                    @endif
                    @if($type === 'vat' && $sale->customer->vat_number)
                        <div class="detail-item">
                            <span class="detail-label">VAT No:</span>
                            <span class="detail-value">{{ $sale->customer->vat_number }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">#</th>
                        <th style="width: 35%; text-align: left;">Item Description</th>
                        <th style="width: 15%; text-align: left;">Batch</th>
                        <th style="width: 10%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Unit Price</th>
                        <th style="width: 10%; text-align: right;">Disc%</th>
                        <th style="width: 10%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->saleItems as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: left;">
                            <div class="item-name">{{ $item->item->description }}</div>
                            <div class="item-code">Code: {{ $item->item->item_no ?? $item->item->item_code ?? 'N/A' }}</div>
                        </td>
                        <td style="text-align: left;">
                            <div class="batch-info">{{ $item->batch->batch_no }}</div>
                            @if($item->batch->expiry_date)
                                <div class="batch-info">Exp: {{ \Carbon\Carbon::parse($item->batch->expiry_date)->format('M Y') }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ number_format($item->quantity) }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($item->discount, 1) }}</td>
                        <td style="text-align: right;"><strong>{{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-left">
                <!-- Payment Information Box -->
                <div class="payment-section">
                    <div class="payment-title">PAYMENT DETAILS</div>
                    <div class="payment-row">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value">{{ ucfirst($sale->payment_method) }}</span>
                    </div>
                    @if($sale->cash_amount > 0)
                        <div class="payment-row">
                            <span class="detail-label">Cash Received:</span>
                            <span class="detail-value">Rs. {{ number_format($sale->cash_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($sale->card_amount > 0)
                        <div class="payment-row">
                            <span class="detail-label">Card Payment:</span>
                            <span class="detail-value">Rs. {{ number_format($sale->card_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($sale->payment_method === 'cash' && $sale->cash_amount > $sale->total_amount)
                        <div class="payment-row">
                            <span class="detail-label">Change Given:</span>
                            <span class="detail-value">Rs. {{ number_format($sale->cash_amount - $sale->total_amount, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="summary-right">
                @php
                    $subtotal = $sale->saleItems->sum(function($item) {
                        return $item->quantity * $item->unit_price;
                    });
                    $totalDiscount = $sale->saleItems->sum(function($item) {
                        return ($item->quantity * $item->unit_price) * ($item->discount / 100);
                    });
                    $totalVat = $sale->vat_amount;
                @endphp

                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span class="summary-value">Rs. {{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Total Discount:</span>
                    <span class="summary-value">Rs. {{ number_format($totalDiscount, 2) }}</span>
                </div>

                @if($type === 'vat')
                <div class="summary-row">
                    <span class="summary-label">Total VAT:</span>
                    <span class="summary-value">Rs. {{ number_format($totalVat, 2) }}</span>
                </div>
                @endif

                <div class="total-row">
                    <span class="summary-label">GRAND TOTAL:</span>
                    <span class="summary-value">Rs. {{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">THANK YOU FOR YOUR BUSINESS!</div>
        </div>
    </div>
</body>
</html>