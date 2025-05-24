<?php

namespace App\Commons\Enum;

enum PurchasePaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';
}
