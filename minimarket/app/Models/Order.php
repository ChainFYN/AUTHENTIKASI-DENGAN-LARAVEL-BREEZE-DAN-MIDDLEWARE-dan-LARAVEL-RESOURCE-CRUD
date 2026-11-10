<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'gross_amount',
        'customer_name',
        'customer_email',
        'snap_token',
        'payment_type',
        'transaction_status',
        'payload',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'payload' => 'array',
    ];
}

