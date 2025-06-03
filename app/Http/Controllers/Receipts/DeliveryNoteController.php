<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Receipts\DeliveryNote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{

    public function index()
    {
        return DeliveryNote::with(['customer', 'items'])
            ->latest()
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'delivery_address' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'vehicle_number' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:20',
            'items.*.unit_price' => 'nullable|numeric|min:0', // Changed from required
           
            'notes' => 'nullable|string',
            'received_by' => 'nullable|string|max:100',
        ]);

        $validated['delivery_note_number'] = $this->generateDeliveryNoteNumber();

        $deliveryNote = DeliveryNote::create($validated);
        $deliveryNote->items()->createMany($validated['items']);

        return response()->json([
            'message' => 'Delivery note created successfully',
            'data' => $deliveryNote->load('customer', 'items')
        ], 201);
    }

    public function show(DeliveryNote $delivery_note)
    {
        return $delivery_note->load('customer', 'items');
    }

    public function update(Request $request, DeliveryNote $delivery_note)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'delivery_address' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'vehicle_number' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'received_by' => 'nullable|string|max:100',
        ]);

        $delivery_note->update($validated);
        
        // Delete existing items
        $delivery_note->items()->delete();
        // Add new items
        $delivery_note->items()->createMany($validated['items']);

        return response()->json([
            'message' => 'Delivery note updated successfully',
            'data' => $delivery_note->load('customer', 'items')
        ]);
    }

    public function destroy(DeliveryNote $delivery_note)
    {
        $delivery_note->delete();
        return response()->json(['message' => 'Delivery note deleted successfully']);
    }

    public function pdf(DeliveryNote $delivery_note)
    {
        $delivery_note->load('customer', 'items');
        
        $pdf = PDF::loadView('pdf.deliveryNotes', [
            'deliveryNote' => $delivery_note
        ]);

        return $pdf->download("delivery-note-{$delivery_note->delivery_note_number}.pdf");
    }

    protected function generateDeliveryNoteNumber()
    {
        $prefix = 'DN-' . date('Y') . '-';
        $lastNote = DeliveryNote::where('delivery_note_number', 'like', $prefix . '%')
            ->orderBy('delivery_note_number', 'desc')
            ->first();

        if ($lastNote) {
            $lastNumber = (int) str_replace($prefix, '', $lastNote->delivery_note_number);
            return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return $prefix . '00001';
    }




}
