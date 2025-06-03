<!DOCTYPE html>
<html>
<head>
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .company-info {
            flex: 1 1 45%;
            min-width: 250px;
        }
        .document-info {
            flex: 1 1 50%;
            min-width: 300px;
            text-align: right;
            align-self: flex-start;
        }
        .document-info p {
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .customer-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .totals {
            float: right;
            width: 250px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .paid-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 40px;
            color: #28a745;
            opacity: 0.3;
            font-weight: bold;
            border: 4px solid #28a745;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div style="position: relative;">
        <div class="paid-stamp">PAID</div>

        <div class="header">
            <div class="company-info">
                <h2 style="margin-bottom: 5px; color: #2c3e50;">{{ config('app.name') }}</h2>
                <p>{{ config('app.company_address') }}</p>
                <p>Phone: {{ config('app.company->phone') }}</p>
                <p>Email: {{ config('app.company->email') }}</p>
                <p>Tax ID: {{ config('app.company_tax_id') }}</p>
            </div>
            <div class="document-info">
                <h2 style="margin-bottom: 10px; color: #3498db;">RECEIPT</h2>
                <p><strong>Receipt #:</strong> {{ $receipt->receipt_number }}</p>
                <p><strong>Date:</strong> {{ $receipt->date->format('d/m/Y') }}</p>
                @if($receipt->invoice)
                    <p><strong>Invoice #:</strong> {{ $receipt->invoice->invoice_number }}</p>
                @endif
            </div>
        </div>

        <div class="customer-info">
            <h3 style="margin-bottom: 8px; color: #2c3e50;">Received From:</h3>
            <p><strong>{{ $receipt->customer->name }}</strong></p>
            <p>{{ $receipt->customer->address }}</p>
            <p>Phone: {{ $receipt->customer->phone }}</p>
            <p>Email: {{ $receipt->customer->email }}</p>
            @if($receipt->customer->tax_id)
                <p>Tax ID: {{ $receipt->customer->tax_id }}</p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Payment received @if($receipt->invoice)(Invoice #{{ $receipt->invoice->invoice_number }})@endif</td>
                    <td class="text-right">{{ number_format($receipt->amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Method:</strong> {{ $receipt->payment_method }}</td>
                    <td class="text-right"></td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <p style="font-size: 14px; font-weight: bold;">
                <strong>Total Received:</strong> {{ number_format($receipt->amount, 2) }}
            </p>
        </div>

        @if($receipt->notes)
        <div style="margin-top: 20px;">
            <h3 style="margin-bottom: 5px; color: #2c3e50;">Notes</h3>
            <p style="white-space: pre-line;">{{ $receipt->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>This receipt serves as confirmation of payment received.</p>
            <p>{{ config('app.name') }} | {{ config('app.company_website') }}</p>
        </div>
    </div>
</body>
</html>
