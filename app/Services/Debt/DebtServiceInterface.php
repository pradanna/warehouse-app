<?php

namespace App\Services\Debt;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Debt\DebtQuery;

interface DebtServiceInterface
{
    public function findAll(DebtQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function summary(DebtQuery $queryParams): ServiceResponse;
}
