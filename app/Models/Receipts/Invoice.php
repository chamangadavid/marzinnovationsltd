<?php

namespace App\Models\Receipts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    
    protected $table = 'invoices'; // Only if different from 'quotations'

    protected $fillable = [
        'invoice_number', 
        'date', 
        'due_date', 
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
        'due_date' => 'date:Y-m-d',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
