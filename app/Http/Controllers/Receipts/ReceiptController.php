<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Receipts\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function index()
    {
        return Inertia::render('MyMARZ/Receipts/Quotations');
        // return Inertia::render('MyMARZ/Receipts/Index');
    }

    public function getReceipts()
    {
        return Receipt::with(['customer', 'invoice'])
            ->latest()
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string'
        ]);

        $receipt = Receipt::create(array_merge($validated, [
            'user_id' => Auth::id()
        ]));

        return response()->json($receipt->load(['customer', 'invoice']), 201);
    }

    public function show(Receipt $receipt)
    {
        return $receipt->load(['customer', 'invoice']);
    }

    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'date' => 'sometimes|date',
            'customer_id' => 'sometimes|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|string|max:50',
            'notes' => 'nullable|string'
        ]);

        $receipt->update($validated);

        return $receipt->load(['customer', 'invoice']);
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return response()->noContent();
    }

    public function pdf(Receipt $receipt)
    {
        $receipt->load(['customer', 'invoice']);
        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'));
        return $pdf->download("receipt-{$receipt->receipt_number}.pdf");
    }
}
