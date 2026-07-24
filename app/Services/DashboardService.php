<?php

namespace App\Services;

use App\Models\CustomerTransaction;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SalesPayment;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;


class DashboardService
{

    private function paymentsQuery($startDate = null, $endDate = null): Builder
    {
        return SalesPayment::query()
            ->when($startDate, fn($q) =>
            $q->whereDate('created_at',  '>=', $startDate))
            ->when($endDate, fn($q) =>
            $q->whereDate('created_at', '<=', $endDate))
            ->when(! $startDate && ! $endDate, function ($q) {
                $q->whereDate('created_at', now()->today());
            })
        ;
    }

    private function salesQuery($startDate = null, $endDate = null): Builder
    {
        return Sale::query()
            ->when($startDate, fn($q) =>
            $q->whereDate('created_at',  '>=', $startDate))
            ->when($endDate, fn($q) =>
            $q->whereDate('created_at', '<=', $endDate))
            ->when(! $startDate && ! $endDate, function ($q) {
                $q->whereDate('created_at', now()->today());
            });
    }
    private function customerTransactionQuery($startDate = null, $endDate = null): Builder
    {
        return CustomerTransaction::query()
            ->when($startDate, fn($q) =>
            $q->whereDate('created_at',  '>=', $startDate))
            ->when($endDate, fn($q) =>
            $q->whereDate('created_at', '<=', $endDate))
            ->when(! $startDate && ! $endDate, function ($q) {
                $q->whereDate('created_at', now()->today());
            });
    }

    private function remember(string $name, $startDate, $endDate, Closure $callback)
    {
        $start = $startDate ?? 'today';
        $end = $endDate ?? 'today';

        return Cache::remember(
            "dashboard.{$name}.{$start}.{$end}",
            now()->addMinutes(5),
            $callback
        );
    }

    // SATIŞ
    public function allSales($startDate = null, $endDate = null)
    {
        return $this->remember('sales', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->salesQuery($startDate, $endDate)
                ->sum('total');
        });
    }

    public function completedSales($startDate = null, $endDate = null)
    {
        return $this->remember('competedSales', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->salesQuery($startDate, $endDate)
                ->where('status', 'completed')
                ->sum('total');
        });
    }

    public function partialSales($startDate = null, $endDate = null)
    {
        return $this->remember('partialSales', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->salesQuery($startDate, $endDate)
                ->where('status', 'partial')
                ->sum('total');
        });
    }
    public function creditSales($startDate = null, $endDate = null)
    {
        return $this->remember('creditSales', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->salesQuery($startDate, $endDate)
                ->where('status', 'unpaid')
                ->sum('total');
        });
    }


    // KASSA

    public function totalPayments($startDate = null, $endDate = null)
    {
        return $this->remember('totalPayments', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->paymentsQuery($startDate, $endDate)
                // ->where('payment_method_id', PaymentMethod::cashId())
                ->sum('amount');
        });
    }

    public function cashPayments($startDate = null, $endDate = null)
    {
        return $this->remember('cashPayments', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->paymentsQuery($startDate, $endDate)
                ->where('payment_method_id', PaymentMethod::cashId())
                ->sum('amount');
        });
    }

    public function cardPayments($startDate = null, $endDate = null)
    {
        return $this->remember('cardPayments', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->paymentsQuery($startDate, $endDate)
                ->where('payment_method_id', PaymentMethod::cardId())
                ->sum('amount');
        });
    }





    // BORC

    public function partialDebt($startDate = null, $endDate = null)
    {
        return $this->remember('partialDebt', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->customerTransactionQuery($startDate, $endDate)
                ->where('type', 'debt')
                ->whereHas('sale', function ($q) {
                    $q->where('status', 'partial');
                })
                ->sum('amount');
        });
    }

    public function creditDebt($startDate = null, $endDate = null)
    {
        return $this->remember('creditDebt', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->customerTransactionQuery($startDate, $endDate)
                ->where('type', 'debt')
                ->whereHas('sale', function ($q) {
                    $q->where('status', 'unpaid');
                })
                ->sum('amount');
        });
    }
    public function debtPayments($startDate = null, $endDate = null)
    {
        return $this->remember('debtPayments', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->customerTransactionQuery($startDate, $endDate)
                ->where('type', 'payment')
                ->sum('amount');
        });
    }

    public function currentDebt()
    {
        return Cache::remember('currentDebt',  now()->addMinutes(5), function () {
            return CustomerTransaction::query()
                ->selectRaw("
                    SUM(
                CASE
                 WHEN type = 'debt' THEN amount
                 WHEN type = 'payment' THEN -amount
                 ELSE 0
                 END
                ) as total
                 ")
                ->value('total') ?? 0;
        });
    }
}
