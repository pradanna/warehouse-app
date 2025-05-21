<?php

namespace App\Commons\Enum;

enum InventoryMovementType: string
{
    case Purhcase = 'purchase';
    case Sale = 'sale';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';
    case Return = 'return';
    case Conversion = 'conversion';
    case Wastage = 'wastage';
}
