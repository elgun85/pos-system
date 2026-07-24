<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class SalesPayment extends Model
{
    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'amount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    #[Scope]
    protected function today(Builder $query): void
    {
        $query->whereDate('created_at', today());
    }
}
