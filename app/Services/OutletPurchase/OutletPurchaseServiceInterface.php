<?php

namespace App\Services\OutletPurchase;

use App\Commons\Http\ServiceResponse;
use App\Schemas\OutletPurchase\OutletPurchaseQuery;
use App\Schemas\OutletPurchase\OutletPurchaseSchema;

interface OutletPurchaseServiceInterface
{
    public function findAll(OutletPurchaseQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function create(OutletPurchaseSchema $schema): ServiceResponse;
    public function update($id, OutletPurchaseSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
