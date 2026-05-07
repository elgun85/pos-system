<?php

namespace App\Models;

use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'payment_method_id',
        'total',
        'paid_amount',
        'discount',
    ];

    public function items()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
