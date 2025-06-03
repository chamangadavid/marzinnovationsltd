<?php

namespace App\Models\Receipts;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable = [
        'delivery_note_number',
        'date',
        'customer_id',
        'delivery_address',
        'reference_number',
        'vehicle_number',
        'notes',
        'received_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with DeliveryNoteItems
     */
    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    /**
     * Calculate total amount for the delivery note
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }
}
