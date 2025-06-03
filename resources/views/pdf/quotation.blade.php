<!DOCTYPE html>
<html>
<head>
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .company-info { width: 60%; }
        .document-info { width: 35%; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .totals { float: right; width: 250px; margin-top: 15px; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; }
        .customer-info { margin-bottom: 15px; }
        .terms-conditions { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2 style="margin-bottom: 5px;">{{ config('app.name') }}</h2>
            <p>123 Business Street, City, Country</p>
            <p>Phone: +1234567890 | Email: info@company.com</p>
            <p>Tax ID: 123456789</p>
        </div>
        <div class="document-info">
            <h2 style="margin-bottom: 5px;">QUOTATION</h2>
            <p><strong>Quotation #:</strong> {{ $quotation->quotation_number }}</p>
            <p><strong>Date:</strong> {{ $quotation->date->format('d/m/Y') }}</p>
            <p><strong>Expiry Date:</strong> {{ $quotation->expiry_date->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="customer-info">
        <h3 style="margin-bottom: 5px;">To:</h3>
        <p><strong>{{ $quotation->customer->name }}</strong></p>
        <p>{{ $quotation->customer->address }}</p>
        <p>Phone: {{ $quotation->customer->phone }}</p>
        <p>Email: {{ $quotation->customer->email }}</p>
        @if($quotation->customer->tax_id)
        <p>Tax ID: {{ $quotation->customer->tax_id }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Sub Total:</strong> {{ number_format($quotation->subtotal, 2) }}</p>
        <p><strong>Tax:</strong> {{ number_format($quotation->tax, 2) }}</p>
        <p style="font-size: 14px; font-weight: bold;">
            <strong>Total:</strong> {{ number_format($quotation->total, 2) }}
        </p>
    </div>

    @if($quotation->terms)
    <div class="terms-conditions">
        <h3>Terms & Conditions</h3>
        <p style="white-space: pre-line;">{{ $quotation->terms }}</p>
    </div>
    @endif

    @if($quotation->notes)
    <div class="notes">
        <h3>Notes</h3>
        <p style="white-space: pre-line;">{{ $quotation->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ config('app.name') }} | www.yourcompany.com</p>
    </div>
</body>
</html>