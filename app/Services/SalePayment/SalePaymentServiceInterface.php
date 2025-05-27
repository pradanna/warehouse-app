<?php

namespace App\Services\SalePayment;

use App\Commons\Http\ServiceResponse;
use App\Schemas\SalePayment\SalePaymentQuery;
use App\Schemas\SalePayment\SalePaymentSchema;

interface SalePaymentServiceInterface
{
    public function create(SalePaymentSchema $schema): ServiceResponse;
    public function findAll(SalePaymentQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
}
