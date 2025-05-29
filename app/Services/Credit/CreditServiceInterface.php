<?php

namespace App\Services\Credit;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Credit\CreditQuery;

interface CreditServiceInterface
{
    public function findAll(CreditQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function summary(CreditQuery $queryParams): ServiceResponse;
}
