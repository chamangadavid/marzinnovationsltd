<!DOCTYPE html>
<html>

<head>
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .company-info {
            width: 60%;
        }
         .company-info img {
            max-width: 100px; /* Reduced from 150px */
            /* height: auto; */
            margin-bottom: 2px;
        }

        .document-info {
            margin-top: -400px;
            width: 100%;
            text-align: right;
        }

        .supplier-info {
            margin-top: 100px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .delivery-info {
            margin-bottom: 20px;
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

        th,
        td {
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

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .draft {
            background-color: #f0f0f0;
            color: #555;
        }

        .sent {
            background-color: #d4e6ff;
            color: #0056b3;
        }

        .approved {
            background-color: #d4edda;
            color: #155724;
        }

        .received {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-info">
             <img src="{{ public_path('assets/marz-logo.png') }}" alt="Company Logo">
            <h2 style="margin-bottom: 5px; color: #2c3e50;">{{ config('app.name') }}</h2>
             <p>Permanent House, Cairo Road 2nd Floor 253A, Lusaka, Zambia</p>
                <p>Phone: +260 966 390 807 | Email: info@marzinnovationsltd.com</p>
                <p>Tpin #: 2003431233</p>
        </div>
        <div class="document-info">
            <h2 style="margin-bottom: 10px; color: #3498db;">PURCHASE ORDER</h2>
            <p><strong>PO #:</strong> {{ $purchaseOrder->po_number }}</p>
            <p><strong>Date:</strong> {{ $purchaseOrder->date->format('d/m/Y') }}</p>
            <p><strong>Status:</strong>
                <span class="status-badge {{ $purchaseOrder->status }}">
                    {{ strtoupper($purchaseOrder->status) }}
                </span>
            </p>
            @if($purchaseOrder->expected_delivery_date)
                <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <div class="supplier-info">
        <h3 style="margin-bottom: 8px; color: #2c3e50;">Supplier:</h3>
        <p><strong>{{ $purchaseOrder->supplier->name }}</strong></p>
        <p>{{ $purchaseOrder->supplier->address }}</p>
        <p>Phone: {{ $purchaseOrder->supplier->phone }}</p>
        <p>Email: {{ $purchaseOrder->supplier->email }}</p>
        @if($purchaseOrder->supplier->tax_id)
            <p>Tax ID: {{ $purchaseOrder->supplier->tax_id }}</p>
        @endif
    </div>

    <div class="delivery-info">
        <h3 style="margin-bottom: 8px; color: #2c3e50;">Delivery Address:</h3>
        <p style="white-space: pre-line;">{{ $purchaseOrder->delivery_address }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Unit Price</th>
                <th style="width: 10%; text-align: right;">Tax Rate</th>
                <th style="width: 15%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 3) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->tax_rate, 2) }}%</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Subtotal:</strong> {{ number_format($purchaseOrder->subtotal, 2) }}</p>
        <p><strong>Tax:</strong> {{ number_format($purchaseOrder->tax, 2) }}</p>
        <p style="font-size: 14px; font-weight: bold;">
            <strong>Total:</strong> {{ number_format($purchaseOrder->total, 2) }}
        </p>
    </div>

    @if($purchaseOrder->terms)
        <div style="margin-top: 20px;">
            <h3 style="margin-bottom: 5px; color: #2c3e50;">Terms & Conditions</h3>
            <p style="white-space: pre-line;">{{ $purchaseOrder->terms }}</p>
        </div>
    @endif

    {{-- @if($purchaseOrder->notes)
        <div style="margin-top: 20px;">
            <h3 style="margin-bottom: 5px; color: #2c3e50;">Notes</h3>
            <p style="white-space: pre-line;">{{ $purchaseOrder->notes }}</p>
        </div>
    @endif --}}

    <div class="footer">
        <p>Creating Value Togther!</p>
        <p>{{ config('app.name') }} </p>
    </div>
</body>

</html>