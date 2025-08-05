<?php

namespace App\Services\Inventory;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Inventory\InventoryQuery;
use App\Schemas\Inventory\InventorySchema;

interface InventoryServiceInterface
{
    public function create(InventorySchema $schema): ServiceResponse;
    public function findAll(InventoryQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, InventorySchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
    public function findBySku($sku): ServiceResponse;
}
