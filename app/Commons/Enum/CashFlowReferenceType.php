<?php

namespace App\Commons\Enum;

enum CashFlowReferenceType: string
{
    case OutletIncome = 'outlet-income';
    case OutletPurchase = 'outlet-purchase';
}
