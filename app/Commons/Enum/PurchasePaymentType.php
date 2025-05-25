<?php

namespace App\Commons\Enum;

enum PurchasePaymentType: string
{
    case Cash = 'cash';
    case Installment = 'installment';
}
