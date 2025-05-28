<?php

namespace App\Services\InventoryAdjustment;

use App\Commons\Http\ServiceResponse;
use App\Schemas\InventoryAdjustment\InventoryAdjustmentQuery;
use App\Schemas\InventoryAdjustment\InventoryAdjustmentSchema;

interface InventoryAdjustmentServiceInterface
{
    public function create(InventoryAdjustmentSchema $schema): ServiceResponse;
    public function findAll(InventoryAdjustmentQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
}
