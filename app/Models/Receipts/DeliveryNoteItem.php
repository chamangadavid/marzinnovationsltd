<?php

namespace App\Models\Receipts;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteItem extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'description',
        'quantity',
        'unit',
        'unit_price'
    ];

    /**
     * Relationship with DeliveryNote
     */
    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    /**
     * Calculate item total
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
