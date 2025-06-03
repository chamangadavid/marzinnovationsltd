<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transactionId',
        'transactionName',
        'clientName',
        'clientEmail',
        'clientMobile',
        'clientTpin',
        'clientAddress',
        'status',
        'quantity',
        'unitPrice',
        'totalAmount',
        'initial_pay',
        'balance',

    ];

    protected $casts = [
        'status' => 'string'
    ];
    
    public static $statuses = [
        'Paid',
        'Pending',
        'Partially_Paid'
    ];

    
    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($transaction) {
            $transaction->totalAmount = $transaction->quantity * $transaction->unitPrice;
            $transaction->balance = $transaction->totalAmount - $transaction->initial_pay;
        });
    
        static::updating(function ($transaction) {
            if ($transaction->isDirty(['quantity', 'unitPrice'])) {
                $transaction->totalAmount = $transaction->quantity * $transaction->unitPrice;
            }
            if ($transaction->isDirty(['initial_pay', 'totalAmount'])) {
                $transaction->balance = $transaction->totalAmount - $transaction->initial_pay;
            }
        });
    }
}
