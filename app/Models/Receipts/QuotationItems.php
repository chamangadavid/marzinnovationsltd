<?php

namespace App\Models\Receipts;

use Illuminate\Database\Eloquent\Model;

class QuotationItems extends Model
{
    protected $fillable = [
        'quotation_id', 
        'description', 
        'quantity', 
        'unit_price', 
        'total',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

}
