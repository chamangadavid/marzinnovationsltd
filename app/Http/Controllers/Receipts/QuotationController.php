<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Receipts\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
  

    public function index()
    {
        return Quotation::with(['customer', 'items', 'user'])->latest()->get();
    }

    public function store(Request $request)
    {
       //dd($request->all());
        $validated = $request->validate([
            'date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:date',
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
        $expiryDate = \Carbon\Carbon::parse($validated['expiry_date'])->format('Y-m-d');


        // Calculate totals
        $subtotal = collect($validated['items'])->sum(function($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $tax = $validated['tax'] ?? 0;
        $total = $subtotal + $tax;

        $quotation = Quotation::create([
            'date' => $date,
            'expiry_date' => $expiryDate,
            'customer_id' => $validated['customer_id'],
            'terms' => $validated['terms'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => $validated['notes'],
            'user_id' => Auth::id() //auth()->id()
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

        $quotation->items()->createMany($items);

        return $quotation->load(['customer', 'items']);
    }

    public function show(Quotation $quotation)
    {
        return $quotation->load(['customer', 'items', 'user']);
    }


    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:date',
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
        $expiryDate = \Carbon\Carbon::parse($validated['expiry_date'])->format('Y-m-d');
        
        // Calculate totals
        $subtotal = collect($validated['items'])->sum(function($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $tax = $validated['tax'] ?? 0;
        $total = $subtotal + $tax;

        $quotation->update([
            'date' => $date,
            'expiry_date' => $expiryDate,
            'customer_id' => $validated['customer_id'],
            'terms' => $validated['terms'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => $validated['notes']
        ]);

        // Sync items
        $quotation->items()->delete();
        $items = collect($validated['items'])->map(function($item) {
            return [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price']
            ];
        });
        $quotation->items()->createMany($items);

        return $quotation->load(['customer', 'items']);
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return response()->noContent();
    }

    public function pdf(Quotation $quotation)
    {
        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation->load(['customer', 'items'])
        ]);
        
        return $pdf->download("quotation_{$quotation->quotation_number}.pdf");
    }
  
}
