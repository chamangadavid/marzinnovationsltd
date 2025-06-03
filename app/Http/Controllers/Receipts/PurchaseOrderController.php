<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Receipts\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{

    public function index()
    {
        try {
            return PurchaseOrder::with(['supplier:id,name', 'items', 'user:id,name'])
                ->latest()
                ->paginate(10);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch purchase orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'expected_delivery_date' => 'nullable|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'delivery_address' => 'required|string',
                'terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string|max:255',
                'items.*.quantity' => 'required|numeric|min:0.001',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            ]);

            // Start database transaction
            return DB::transaction(function () use ($validated) {
                // Calculate totals
                $subtotal = collect($validated['items'])->sum(function($item) {
                    return $item['quantity'] * $item['unit_price'];
                });
                
                $tax = collect($validated['items'])->sum(function($item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    return $itemTotal * ($taxRate / 100);
                });

                $total = $subtotal + $tax;

                // Create purchase order
                $po = PurchaseOrder::create(array_merge($validated, [
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                    'user_id' => Auth::id(),
                    'status' => 'draft'
                ]));

                // Prepare items
                $items = collect($validated['items'])->map(function($item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $taxAmount = $itemTotal * ($taxRate / 100);
                    
                    return [
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $taxRate,
                        'total' => $itemTotal + $taxAmount
                    ];
                });

                // Create items
                $po->items()->createMany($items->toArray());

                return response()->json([
                    'message' => 'Purchase order created successfully',
                    'data' => $po->load(['supplier', 'items', 'user'])
                ], 201);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create purchase order',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }


    public function show(PurchaseOrder $purchaseOrder)
    {
        try {
            return $purchaseOrder->load(['supplier', 'items', 'user']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $validated = $request->validate([
                'date' => 'sometimes|date',
                'expected_delivery_date' => 'nullable|date',
                'supplier_id' => 'sometimes|exists:suppliers,id',
                'delivery_address' => 'sometimes|string',
                'terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'status' => 'sometimes|in:draft,sent,approved,received,cancelled',
                'items' => 'sometimes|array|min:1',
                'items.*.description' => 'required_with:items|string|max:255',
                'items.*.quantity' => 'required_with:items|numeric|min:0.001',
                'items.*.unit_price' => 'required_with:items|numeric|min:0',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            ]);

            if (isset($validated['items'])) {
                // Recalculate totals if items changed
                $subtotal = collect($validated['items'])->sum(function($item) {
                    return $item['quantity'] * $item['unit_price'];
                });
                
                $tax = collect($validated['items'])->sum(function($item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    return $itemTotal * ($taxRate / 100);
                });

                $total = $subtotal + $tax;

                $validated['subtotal'] = $subtotal;
                $validated['tax'] = $tax;
                $validated['total'] = $total;

                // Delete existing items and create new ones
                $purchaseOrder->items()->delete();

                $items = collect($validated['items'])->map(function($item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $taxAmount = $itemTotal * ($taxRate / 100);
                    
                    return [
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $taxRate,
                        'total' => $itemTotal + $taxAmount
                    ];
                });

                $purchaseOrder->items()->createMany($items);
            }

            $purchaseOrder->update($validated);

            return response()->json([
                'message' => 'Purchase order updated successfully',
                'data' => $purchaseOrder->fresh()->load(['supplier', 'items', 'user'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->delete();
            return response()->json([
                'message' => 'Purchase order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pdf(PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->load(['supplier', 'items', 'user']);
            $pdf = PDF::loadView('pdf.purchaseOrders', compact('purchaseOrder'));
            return $pdf->download("purchase-order-{$purchaseOrder->po_number}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function generatePONumber()
    {
        $prefix = 'PO-' . date('Y') . '-';
        $lastPO = PurchaseOrder::where('po_number', 'like', $prefix . '%')
            ->orderBy('po_number', 'desc')
            ->first();

        if ($lastPO) {
            $lastNumber = (int) str_replace($prefix, '', $lastPO->po_number);
            return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return $prefix . '00001';
    }
}
