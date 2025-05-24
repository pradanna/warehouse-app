<?php

namespace App\Services\Purchase;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Purchase\PurchasePaymentSchema;
use App\Schemas\Purchase\PurchaseQuery;
use App\Schemas\Purchase\PurchaseSchema;

interface PurchaseServiceInterface
{
    public function create(PurchaseSchema $schema): ServiceResponse;
    public function findAll(PurchaseQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function payment($id, PurchasePaymentSchema $schema): ServiceResponse;
}
