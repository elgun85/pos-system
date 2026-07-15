<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'payment_method_id',
        'sale_id',
        'user_id',
        'type',
        'amount',
        'notes',
    ];
    
}
