<!DOCTYPE html>
<html>
<head>
    <title>Transaction #{{ $transaction->transactionId }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .header img { height: 80px; margin-bottom: 10px; }
        .header h4 { margin: 5px 0; color: #333; font-size: 24px; }
        .details { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details th, .details td { border: 1px solid #ddd; padding: 10px; }
        .details th { background-color: #f2f2f2; text-align: left; width: 30%; }
        .total { font-weight: bold; font-size: 1.1em; }
        .paid { color: #28a745; }
        .pending { color: #ffc107; }
        .partially-paid { color: #17a2b8; }
        .barcode { text-align: center; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; }
        .document-title { text-align: center; font-size: 18px; margin: 15px 0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <img src="../public/assets/marz-logo.png" alt="Logo" style="height: 100px">
        <h4>MARZ INNOVATIONS LIMITED</h4>
    </div>

    <div class="document-title">
        TRANSACTION RECEIPT
    </div>
    <div class="barcode">
        <?php
        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($transaction->transactionId, 'C128A') . '" alt="barcode">';
        ?>
    </div>

    <div class="barcode">
        <barcode code="{{ $transaction->transactionId }}" type="C128A" size="1.5" height="40" />
    </div>

    <table class="details">
        <tr>
            <th>Transaction ID</th>
            <td>{{ $transaction->transactionId }}</td>
        </tr>
        <tr>
            <th>Transaction Date</th>
            <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <th>Transaction Name</th>
            <td>{{ $transaction->transactionName }}</td>
        </tr>
        <tr>
            <th>Client Name</th>
            <td>{{ $transaction->clientName }}</td>
        </tr>
        <tr>
            <th>Client Email</th>
            <td>{{ $transaction->clientEmail }}</td>
        </tr>
        <tr>
            <th>Client Mobile</th>
            <td>{{ $transaction->clientMobile }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $transaction->quantity }}</td>
        </tr>
        <tr>
            <th>Unit Price</th>
            <td>K{{ number_format($transaction->unitPrice, 2) }}</td>
        </tr>
        <tr class="total">
            <th>Total Amount</th>
            <td>K{{ number_format($transaction->totalAmount, 2) }}</td>
        </tr>
        <tr>
            <th>Initial Payment</th>
            <td>K{{ number_format($transaction->initial_pay, 2) }}</td>
        </tr>
        <tr class="total">
            <th>Balance</th>
            <td>K{{ number_format($transaction->balance, 2) }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td class="{{ strtolower(str_replace('_', '-', $transaction->status)) }}">
                {{ $transaction->status }}
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>This document confirms the payments made to Marz Innovation Limited</p>
        <p>For any queries please feel free to contact us at: info@marzinnovation.com</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>




{{-- <!DOCTYPE html>
<html>
<head>
    <title>Transaction #{{ $transaction->transactionId }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #333; }
        .details { width: 100%; border-collapse: collapse; }
        .details th, .details td { border: 1px solid #ddd; padding: 8px; }
        .details th { background-color: #f2f2f2; text-align: left; }
        .total { font-weight: bold; font-size: 1.1em; }
        .paid { color: green; }
        .pending { color: orange; }
        .partially-paid { color: blue; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Transaction Receipt</h1>
        <p>Date: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table class="details">
        <tr>
            <th>Transaction ID</th>
            <td>{{ $transaction->transactionId }}</td>
        </tr>
        <tr>
            <th>Transaction Name</th>
            <td>{{ $transaction->transactionName }}</td>
        </tr>
        <tr>
            <th>Client Name</th>
            <td>{{ $transaction->clientName }}</td>
        </tr>
        <tr>
            <th>Client Email</th>
            <td>{{ $transaction->clientEmail }}</td>
        </tr>
        <tr>
            <th>Client Mobile</th>
            <td>{{ $transaction->clientMobile }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $transaction->quantity }}</td>
        </tr>
        <tr>
            <th>Unit Price</th>
            <td>K{{ number_format($transaction->unitPrice, 2) }}</td>
        </tr>
        <tr class="total">
            <th>Total Amount</th>
            <td>K{{ number_format($transaction->totalAmount, 2) }}</td>
        </tr>
        <tr>
            <th>Initial Payment</th>
            <td>K{{ number_format($transaction->initial_pay, 2) }}</td>
        </tr>
        <tr class="total">
            <th>Balance</th>
            <td>K{{ number_format($transaction->balance, 2) }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td class="{{ strtolower(str_replace('_', '-', $transaction->status)) }}">
                {{ $transaction->status }}
            </td>
        </tr>
        <tr>
            <th>Transaction Date</th>
            <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
    </table>
</body>
</html> --}}