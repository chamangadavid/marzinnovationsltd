<?php

namespace App\Models\Receipts;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders'; // Only if different from 'quotations'

    protected $fillable = [
        'po_number',
        'date',
        'expected_delivery_date',
        'supplier_id',
        'delivery_address',
        'terms',
        'notes',
        'subtotal',
        'tax',
        'total',
        'status',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($po) {
            $po->po_number = $po->po_number ?? static::generatePONumber();
            $po->user_id = $po->user_id ?? Auth::id();
        });
    }

    public static function generatePONumber()
    {
        $prefix = 'PO-' . date('Y') . '-';
        $latest = static::where('po_number', 'like', $prefix . '%')
            ->orderBy('po_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) str_replace($prefix, '', $latest->po_number);
            return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return $prefix . '00001';
    }

    // public function items()
    // {
    //     return $this->hasMany(PurchaseOrderItem::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($po) {
    //         $po->po_number = static::generatePONumber();
    //     });
    // }

    // public static function generatePONumber()
    // {
    //     $prefix = 'PO-';
    //     $latest = static::latest()->first();
    //     $number = $latest ? (int) str_replace($prefix, '', $latest->po_number) + 1 : 1;
    //     return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    // }



}
