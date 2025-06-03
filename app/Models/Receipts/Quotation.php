<?php

namespace App\Models\Receipts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{

    protected $table = 'quotations'; // Only if different from 'quotations'

    protected $fillable = [
        'quotation_number', 
        'date', 
        'expiry_date', 
        'customer_id',
        'terms', 
        'subtotal', 
        'tax', 
        'total', 
        'notes', 
        'user_id'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'expiry_date' => 'date:Y-m-d',
    ];

    protected static function booted()
    {
        static::creating(function ($quotation) {
            $quotation->quotation_number = 'QT-' . date('Ymd') . '-' . str_pad(Quotation::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItems::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
