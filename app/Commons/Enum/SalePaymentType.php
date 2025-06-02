<?php

namespace App\Commons\Enum;

enum SalePaymentType: string
{
    case Cash = 'cash';
    case Installment = 'installment';
}
