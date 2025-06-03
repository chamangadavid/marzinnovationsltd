<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Receipts\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    public function index()
    {
        return Invoice::with(['customer', 'items', 'user'])->latest()->get();
    }

    public function store(Request $request)
    {
      
        $validated = $request->validate([
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'tax' => 'nullable|numeric|min:0'
        ]);

        // Format dates to Y-m-d format
        $date = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');
        $expiryDate = \Carbon\Carbon::parse($validated['due_date'])->format('Y-m-d');


        // Calculate totals
        $subtotal = collect($validated['items'])->sum(function($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $tax = $validated['tax'] ?? 0;
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'date' => $date,
            'due_date' => $expiryDate,
            'customer_id' => $validated['customer_id'],
            'terms' => $validated['terms'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => $validated['notes'],
            'user_id' => Auth::id() 
        ]);

        // Create items
        $items = collect($validated['items'])->map(function($item) {
            return [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price']
            ];
        });

        $invoice->items()->createMany($items);

        return $invoice->load(['customer', 'items']);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['customer', 'items', 'user']);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'tax' => 'nullable|numeric|min:0'
        ]);

        // Format dates to Y-m-d format
        $date = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');
        $expiryDate = \Carbon\Carbon::parse($validated['due_date'])->format('Y-m-d');
        
        // Calculate totals
        $subtotal = collect($validated['items'])->sum(function($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $tax = $validated['tax'] ?? 0;
        $total = $subtotal + $tax;

        $invoice->update([
            'date' => $date,
            'due_date' => $expiryDate,
            'customer_id' => $validated['customer_id'],
            'terms' => $validated['terms'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => $validated['notes']
        ]);

        // Sync items
        $invoice->items()->delete();
        $items = collect($validated['items'])->map(function($item) {
            return [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price']
            ];
        });
        $invoice->items()->createMany($items);

        return $invoice->load(['customer', 'items']);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->noContent();
    }

    public function invoicePdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load(['customer', 'items'])
        ]);
        
        return $pdf->download("invoice_{$invoice->invoice_number}.pdf");
    }


}
