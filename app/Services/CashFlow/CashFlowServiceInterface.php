<?php

namespace App\Services\CashFlow;

use App\Commons\Http\ServiceResponse;
use App\Schemas\CashFlow\CashFlowQuery;
use App\Schemas\CashFlow\CashFlowSummaryQuery;

interface CashFlowServiceInterface
{
    public function findAll(CashFlowQuery $queryParams): ServiceResponse;
    public function summary(CashFlowSummaryQuery $queryParams): ServiceResponse;
}
