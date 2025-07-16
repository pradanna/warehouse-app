<?php

namespace App\Services\CashFlow;

use App\Commons\Http\ServiceResponse;
use App\Schemas\CashFlow\CashFlowQuery;

interface CashFlowServiceInterface
{
    public function findAll(CashFlowQuery $queryParams): ServiceResponse;
}
