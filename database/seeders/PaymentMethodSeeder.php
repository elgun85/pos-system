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
            [
                'code' => PaymentMethodCode::CREDIT,
                'name' => __('resource.payment.nameCredit'),
                'description' => __('resource.payment.nameCreditDesk'),
                'status' => true,
            ],
            [
                'code' => PaymentMethodCode::PARTIAL,
                'name' => __('resource.payment.namePartial'),
                'description' => __('resource.payment.namePartialDesk'),
                'status' => true,
            ],
            [
                'code' => PaymentMethodCode::GIFT,
                'name' => __('resource.payment.nameGift'),
                'description' => __('resource.payment.nameGiftDesk'),
                'status' => false, // Lazım gəldikdə admin paneldən aktiv edilə bilər
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
