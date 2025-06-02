<?php

namespace App\Services\InventoryMovement;

use App\Commons\Http\ServiceResponse;
use App\Schemas\InventoryMovement\InventoryMovementQuery;

interface InventoryMovementServiceInterface
{
    public function findAll(InventoryMovementQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
}
