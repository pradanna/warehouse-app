<?php

namespace App\Services\Inventory;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Inventory\InventoryQuery;
use App\Schemas\Inventory\InventorySchema;
use Illuminate\Contracts\Support\Responsable;

interface InventoryServiceInterface
{
    public function create(InventorySchema $schema): Responsable;
    public function findAll(InventoryQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
    public function patch($id, InventorySchema $schema): Responsable;
    public function delete($id): Responsable;
}
