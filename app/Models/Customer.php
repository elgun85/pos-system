<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'email', 'phone', 'address', 'points', 'status'];

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    public function debts()
    {
        return $this->hasMany(CustomerTransaction::class)
            ->where('type', 'debt');
    }

    public function payments()
    {
        return $this->hasMany(CustomerTransaction::class)
            ->where('type', 'payment');
    }
// --- ACCESSOR (Əgər SQL Scope çağırılmayıbsa zəmanət üçün) ---

    /**
     * Əgər SQL Query-də withTotalDebt() çağırılmayıbsa,
     * $customer->total_debt çağırılanda PHP tərəfində hesablayır.
     */
    protected function totalDebt(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Əgər SQL query artıq 'total_debt' sütununu gətiribsə, onu işlət
                if (array_key_exists('total_debt', $attributes)) {
                    return (float) $attributes['total_debt'];
                }

                // Əks halda münasibət üzərindən canlı hesabla
                $totalDebt = $this->debts()->sum('amount');
                $totalPayment = $this->payments()->sum('amount');

                return max(0, $totalDebt - $totalPayment);
            }
        );
    }

    #[Scope]
    protected function withTotalDebt(Builder $query): void
    {
        $query->select('customers.*')
            ->selectSub(function ($q) {
                $q->from('customer_transactions')
                    ->selectRaw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN type = 'debt' THEN amount
                                WHEN type = 'payment' THEN -amount
                                ELSE 0
                            END
                        ), 0
                    )
                ")
                    ->whereColumn(
                        'customer_transactions.customer_id',
                        'customers.id'
                    );
            }, 'total_debt');
    }


    #[Scope]
    protected function debtors(Builder $query): void
    {
        $query
            ->withTotalDebt()
            ->having('total_debt', '>', 0);
    }
}
