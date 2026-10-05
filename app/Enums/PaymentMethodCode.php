<?php

namespace App\Enums;

enum PaymentMethodCode: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case CREDIT = 'credit';     // Nisyə
    case PARTIAL = 'partial';   // Hissə-hissə
    case GIFT = 'gift';         // Hədiyyə kartı / Bonus
}
