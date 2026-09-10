<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {

            $expense = Expense::create([
                'expense_category_id' =>     $data['expense_category_id'],
                'payment_method_id'   =>     $data['payment_method_id'],
                'user_id'             =>      auth()->id(),
                'amount'              =>     $data['amount'],
                'notes'               =>     $data['notes'] ?? null,
            ]);

            return $expense;
        });
    }
}
