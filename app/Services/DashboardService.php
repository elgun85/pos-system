<?php

namespace App\Services;

use App\Models\CustomerTransaction;
use App\Models\Damage;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class DashboardService
{


    private function paymentsQuery($startDate = null, $endDate = null): Builder
    {
        return SalesPayment::query()
            ->whereHas('sale', function ($q) {
                $q->whereNotIn('status', ['cancelled', 'draft']); // whereNotIn istifadə olunmalıdır
            })
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->when(!$startDate && !$endDate, fn($q) => $q->whereDate('created_at', now()->today()));
    }

    private function salesQuery($startDate = null, $endDate = null): Builder
    {
        return Sale::query()
            ->whereNotIn('status', ['cancelled', 'draft'])
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
            now()->addMinutes(1),
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

    public function totalReturns($startDate = null, $endDate = null)
    {
        return $this->remember('totalReturns', $startDate, $endDate, function () use ($startDate, $endDate) {
            return ReturnItem::query()
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->when(!$startDate && ! $endDate, fn($q) => $q->whereDate('created_at', now()->today()))
                ->sum('refund_amount')
            ;
        });
    }

    public function netSales($startDate = null, $endDate = null)
    {
        return $this->allSales($startDate, $endDate) - $this->totalReturns($startDate, $endDate);
    }

    public function netProfit($startDate = null, $endDate = null)
    {
        return $this->remember('netProfit', $startDate, $endDate, function () use ($startDate, $endDate) {
            // 1) Xalis satış gəliri: endirim artıq sales.total-dadır
            $revenue = $this->allSales($startDate, $endDate);

            // 2) Satılan malın maya dəyəri
            $soldCost  = SalesItem::query()
                ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                    $q->whereNotIn('status', ['cancelled', 'draft'])
                        ->when($startDate, fn($sq) => $sq->whereDate('created_at', '>=', $startDate))
                        ->when($endDate, fn($sq) => $sq->whereDate('created_at', '<=', $endDate))
                        ->when(!$startDate && !$endDate, fn($sq) => $sq->whereDate('created_at', now()->today()));
                })
                ->selectRaw('SUM(cost_price * quantity) as cost')
                ->value('cost') ?? 0;
            // 3) Qaytarmalar: gəlir geri qaytarılır, mal anbara qayıdır (maya dəyəri geri gəlir)
            $returns = $this->totalReturns($startDate, $endDate);

            $returnedCost = ReturnItem::query()
                ->join('returns', 'returns.id', '=', 'return_items.return_id')
                ->join('sales_items', function ($join) {
                    $join->on('sales_items.sale_id', '=', 'returns.sale_id')
                        ->on('sales_items.product_id', '=', 'return_items.product_id');
                })
                ->when($startDate, fn($q) => $q->whereDate('return_items.created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('return_items.created_at', '<=', $endDate))
                ->when(!$startDate && !$endDate, fn($q) => $q->whereDate('return_items.created_at', now()->today()))
                ->selectRaw('SUM(return_items.quantity * sales_items.cost_price) as cost')
                ->value('cost') ?? 0;


            return $revenue - $soldCost - $returns + $returnedCost
                - $this->totalDamages($startDate, $endDate)
                - $this->totalExpenses($startDate, $endDate);
        });
    }

    public function totalDamages($startDate = null, $endDate = null)
    {
        return $this->remember('totalDamages', $startDate, $endDate, function () use ($startDate, $endDate) {
            return Damage::query()
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->when(!$startDate && !$endDate, fn($q) => $q->whereDate('created_at', now()->today()))
                ->sum('total_cost');
        });
    }

    public function totalExpenses($startDate = null, $endDate = null)
    {
        return $this->remember('totalExpenses', $startDate, $endDate, function () use ($startDate, $endDate) {
            return Expense::query()
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->when(!$startDate && !$endDate, fn($q) => $q->whereDate('created_at', now()->today()))
                ->sum('amount')
            ;
        });
    }

    public function expensesByCategory($startDate = null, $endDate = null)
    {
        /*         return $this->remember('expensesByCategory', $startDate, $endDate, function () use ($startDate, $endDate) {

        }); */

        return Expense::query()
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select(
                'expense_categories.name as category_name',
                DB::raw(('SuM(expenses.amount) as total_amount'))
            )
            ->when($startDate, fn($q) => $q->whereDate('expenses.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('expenses.created_at', '<=', $endDate))
            ->when(!$startDate && !$endDate, fn($q) => $q->whereDate('expenses.created_at', now()->today()))
            ->groupBy('expense_categories.id', 'expense_categories.name')
            //  ->whereNotIn('expense_categories.name',['Yeni Mehsul'])
            ->orderByDesc('total_amount')
            ->get()
            ->pluck('total_amount', 'category_name')
        ;
    }



    // KASSA

    public function totalPayments($startDate = null, $endDate = null)
    {
        return $this->remember('totalPayments', $startDate, $endDate, function () use ($startDate, $endDate) {
            return $this->paymentsQuery($startDate, $endDate)
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

    public function paymentsByDays($startDate = null, $endDate = null)
    {
        return $this->paymentsQuery($startDate, $endDate)
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('SUM(amount) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();
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
                 WHEN type IN ('payment','refund') THEN -amount
                 ELSE 0
                 END
                ) as total
                 ")
                ->value('total') ?? 0;
        });
    }
}
