<?php

namespace App\Models\Receipts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts'; // Only if different from 'quotations'

    protected $fillable = [
        'receipt_number',
        'date',
        'customer_id',
        'invoice_id',
        'amount',
        'payment_method',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            $receipt->receipt_number = static::generateReceiptNumber();
        });
    }

    public static function generateReceiptNumber()
    {
        $prefix = 'RCT-';
        $latest = static::latest()->first();
        $number = $latest ? (int) str_replace($prefix, '', $latest->receipt_number) + 1 : 1;
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    
}
