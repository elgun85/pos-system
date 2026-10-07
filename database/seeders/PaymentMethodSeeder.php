<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodCode;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'code'           =>          PaymentMethodCode::CASH,
                'name'           =>          __('resource.payment.nameCash'),
                'description'    =>          __('resource.payment.nameCashDesk'),
                'status'         =>          true,
            ],
            [
                'code'           =>          PaymentMethodCode::CARD,
                'name'           =>          __('resource.payment.nameCard'),
                'description'    =>          __('resource.payment.nameCardDesk'),
                'status'         =>          true,
            ],

        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                [
                    'name' => $method['name'],
                    'description' => $method['description'],
                    'status' => $method['status'],
                ],
            );
        }
    }
}
