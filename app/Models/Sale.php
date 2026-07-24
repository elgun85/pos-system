<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'sale_number',
        'customer_id',
        'total',
        'discount',
        'status',
        'user_id',
        'notes',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDueAmountAttribute(): float
    {
        $paid = $this->payments()->sum('amount');
        return max($this->total - $paid, 0);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    public function payments()
    {
        return $this->hasMany(SalesPayment::class);
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    #[Scope]
    protected function today(Builder $query): void
    {
        $query->whereDate('created_at', today());
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    #[Scope]
    protected function partial(Builder $query): void
    {
        $query->where('status', 'partial');
    }

    #[Scope]
    protected function unpaid(Builder $query): void
    {
        $query->where('status', 'unpaid');
    }

}
