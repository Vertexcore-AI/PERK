<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quotation #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }} - Perk Enterprises</title>
    <style>
    @page {
        size: A4;
        margin: 0;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 11px;
        line-height: 1.4;
        color: #333;
        min-height: 29.7cm;
        padding: 0.8cm;
        margin: 0 auto;
        position: relative;
    }

    .container {
        width: 100%;
        min-height: 27.7cm;
        position: relative;
    }

    /* Header Styles */
    .header {
        margin-bottom: 15px;
        border-bottom: 2px solid #e91e63;
        padding-bottom: 10px;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .logo-section {
        flex: 0 0 auto;
    }

    .logo {
        width: 150px;
        height: auto;
    }

    .company-info {
        flex: 1;
        text-align: right;
        margin-left: 30px;
        margin-top: -80px;
    }

    .company-name {
        font-size: 18px;
        font-weight: bold;
        color: #e91e63;
        margin-bottom: 2px;
    }

    .company-tagline {
        font-size: 11px;
        color: #666;
        font-style: italic;
        margin-bottom: 3px;
    }

    .company-address {
        font-size: 9px;
        color: #666;
        line-height: 1.2;
    }

    /* Customer & Quote Details */
    .details-section {
        margin-bottom: 5px;
    }

    .customer-details {
        width: 100%;
        display: table;
        table-layout: fixed;
    }

    .detail-column {
        display: table-cell;
        width: 33.333%;
        vertical-align: top;
        padding-right: 15px;
    }

    .detail-column:last-child {
        padding-right: 0;
    }

    .detail-row {
        margin-bottom: 5px;
        display: block;
    }

    .section-title {
        font-size: 14px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e91e63;
    }

    .detail-label {
        display: inline-block;
        width: 100px;
        color: #666;
        font-size: 11px;
    }

    .detail-value {
        display: inline-block;
        color: #333;
        font-size: 11px;
        font-weight: 500;
    }

    /* Items Table */
    .items-section {
        margin-bottom: 15px;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        table-layout: fixed;
    }

    .items-table thead {
        background-color: #f3f4f6;
    }

    .items-table th {
        padding: 6px 4px;
        text-align: left;
        font-size: 9px;
        font-weight: bold;
        color: #333;
        border: 1px solid #d1d5db;
    }

    .items-table td {
        padding: 4px 4px;
        font-size: 9px;
        border: 1px solid #d1d5db;
    }

    .items-table tfoot td {
        padding: 4px 4px;
        font-size: 10px;
        border: none;
        background-color: #f9fafb;
    }

    .items-table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .item-name {
        font-weight: 600;
        color: #333;
        font-size: 9px;
    }

    .item-code {
        font-size: 8px;
        color: #666;
    }

    .batch-info {
        font-size: 8px;
        color: #666;
    }

    .summary-label {
        color: #666;
    }

    .summary-value {
        color: #333;
        font-weight: 500;
        font-size: 9px;
    }

    .total-row .summary-label {
        font-size: 10px;
        font-weight: bold;
        color: #333;
    }

    .total-row .summary-value {
        font-size: 9px;
        font-weight: bold;
        color: #e91e63;
    }

    .summary-divider td {
        border-top: 1px solid #9ca3af;
        padding: 0;
    }

    /* Terms Section */
    .terms-section {
        padding: 5px;
    }

    .terms-title {
        font-size: 10px;
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
    }

    .terms-list {
        list-style: none;
    }

    .terms-list li {
        font-size: 10px;
        color: #666;
        margin-bottom: 5px;
        padding-left: 15px;
        position: relative;
    }

    .terms-list li:before {
        content: "•";
        position: absolute;
        left: 0;
    }

    /* Footer */
    .footer {
        position: absolute;
        bottom: 0.2cm;
        left: 0.8cm;
        right: 0.8cm;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
    }

    .signature-section {
        flex: 1;
        text-align: right;
    }

    .signature-line {
        display: inline-block;
        width: 200px;
        border-bottom: 1px solid #666;
        margin-top: 40px;
    }

    .signature-text {
        font-size: 10px;
        color: #666;
        margin-top: 5px;
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-row">
                <!-- Left: PERK Logo & Contact -->
                <div class="logo-section">
                    @if(file_exists(public_path('images/logo.jpeg')))
                        <img src="{{ public_path('images/logo.jpeg') }}" alt="Perk Logo" class="logo">
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
                <div class="company-info" style="text-align: right;  margin-top: -150px;">
                    @if(file_exists(public_path('images/IDEAL-MOTORS.jpg')))
                        <img src="{{ public_path('images/IDEAL-MOTORS.jpg') }}" alt="Ideal Motors" style="height: 40px; width: auto; margin-bottom: 5px;">
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

        <!-- Customer & Quote Details -->
        <div class="details-section">
            <div class="customer-details">
                <div class="detail-column">
                    <div class="detail-row">
                        <span class="detail-label">Quotation N0: </span><span class="detail-value">#{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Customer Name:</span>
                        <span class="detail-value">{{ $quotation->customer->name }}</span>
                    </div>
                    @if($quotation->customer->company)
                    <div class="detail-row">
                        <span class="detail-label">Company:</span>
                        <span class="detail-value">{{ $quotation->customer->company }}</span>
                    </div>
                    @endif
                </div>

                <div class="detail-column">
                    @if($quotation->customer->email)
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">{{ $quotation->customer->email }}</span>
                    </div>
                    @endif
                    @if($quotation->customer->address)
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">{{ $quotation->customer->address }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Quote Date:</span>
                        <span class="detail-value">{{ $quotation->quote_date->format('F d, Y') }}</span>
                    </div>
                </div>

                <div class="detail-column">
                    <div class="detail-row">
                        <span class="detail-label">Valid Until:</span>
                        <span class="detail-value">{{ $quotation->valid_until->format('F d, Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Items:</span>
                        <span class="detail-value">{{ $quotation->quoteItems->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="items-section">
            <div class="section-title"></div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Item Description</th>
                        <th style="width: 15%;">Batch Details</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 15%;" class="text-right">Unit Price (LKR)</th>
                        <th style="width: 10%;" class="text-right">Discount</th>
                        <th style="width: 10%;" class="text-right">Total (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->quoteItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->item->description }}</div>
                            <div class="item-code">Code: {{ $item->item->item_code }}</div>
                        </td>
                        <td>
                            <div class="batch-info">{{ $item->batch->batch_no }}</div>
                            @if($item->batch->expiry_date)
                            <div class="batch-info">Exp:
                                {{ \Carbon\Carbon::parse($item->batch->expiry_date)->format('M Y') }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity) }}</td>
                        <td class="text-right"> {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->discount, 2) }}%</td>
                        <td class="text-right"><strong> {{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                    $subtotal = $quotation->quoteItems->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                    });
                    $totalDiscount = $quotation->quoteItems->sum(function($item) {
                    return ($item->quantity * $item->unit_price) * ($item->discount / 100);
                    });
                    $totalVat = $quotation->quoteItems->sum(function($item) {
                    $afterDiscount = ($item->quantity * $item->unit_price) - (($item->quantity * $item->unit_price) *
                    ($item->discount / 100));
                    return $afterDiscount * ($item->vat / 100);
                    });
                    @endphp
                    <tr>
                        <td colspan="6" class="text-right summary-label">Subtotal:</td>
                        <td class="text-right summary-value"> {{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right summary-label">Total Discount:</td>
                        <td class="text-right summary-value"> {{ number_format($totalDiscount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right summary-label">Total VAT:</td>
                        <td class="text-right summary-value"> {{ number_format($totalVat, 2) }}</td>
                    </tr>
                    <tr class="summary-divider">
                        <td colspan="7"></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="6" class="text-right summary-label">GRAND TOTAL (LKR):</td>
                        <td class="text-right summary-value">{{ number_format($quotation->total_estimate, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terms & Conditions -->
        <div class="terms-section">
            <div class="terms-title">TERMS & CONDITIONS</div>
            <ul class="terms-list">
                <li>This quotation is valid until {{ $quotation->valid_until->format('F d, Y') }} and subject to change
                    thereafter.</li>
                <li>All prices are in Sri Lankan Rupees (LKR) and inclusive of applicable taxes unless stated otherwise.
                </li>
                <li>Availability of items is subject to stock at the time of order confirmation.</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-text">Authorized Signature</div>
                    <div class="signature-text">Perk Enterprises Pvt Ltd</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>