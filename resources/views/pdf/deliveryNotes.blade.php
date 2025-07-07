<!DOCTYPE html>
<html>

<head>
    <title>Delivery Note #{{ $deliveryNote->delivery_note_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f2f2f2;
        }

        .signature {
            margin-top: 50px;
        }

        .signature-table {
            width: 100%;
        }

        .signature-table td {
            width: 50%;
            padding: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DELIVERY NOTE</h1>
        <p>No: {{ $deliveryNote->delivery_note_number }}</p>
        <p>Date: {{ $deliveryNote->date->format('d/m/Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Deliver To:</strong><br>
                {{ $deliveryNote->customer->name }}<br>
                {{ $deliveryNote->delivery_address }}<br>
                @if($deliveryNote->customer->phone)
                    Phone: {{ $deliveryNote->customer->phone }}<br>
                @endif
                @if($deliveryNote->customer->tax_id)
                    Tax ID: {{ $deliveryNote->customer->tax_id }}
                @endif
            </td>
            <td width="50%">
                <strong>Details:</strong><br>
                @if($deliveryNote->reference_number)
                    Reference: {{ $deliveryNote->reference_number }}<br>
                @endif
                @if($deliveryNote->vehicle_number)
                    Vehicle: {{ $deliveryNote->vehicle_number }}<br>
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveryNote->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ $item->unit ?? '-' }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>{{ number_format($deliveryNote->total, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($deliveryNote->notes)
        <div style="margin-bottom: 20px;">
            <strong>Notes:</strong><br>
            {{ $deliveryNote->notes }}
        </div>
    @endif

    <div class="signature">
        <table class="signature-table">
            <tr>
                <td>
                    <strong>Prepared By:</strong><br><br>
                    _______________________<br>
                    Name/Signature
                </td>
                <td>
                    <strong>Received By:</strong><br><br>
                    ________________________<br>
                    @if($deliveryNote->received_by)
                        {{ $deliveryNote->received_by }}<br>
                    @endif
                    Name/Signature
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Thank you for your business!
    </div>
</body>

</html>