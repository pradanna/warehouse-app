<?php

namespace App\Commons\Enum;

enum CashFlowType: string
{
    case Debit = 'debit';
    case Credit = 'credit';
}
