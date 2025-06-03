<?php

namespace App\Models\Receipts;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers'; // Only if different from 'quotations'

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'address', 
        'tax_id'
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }

    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

}
