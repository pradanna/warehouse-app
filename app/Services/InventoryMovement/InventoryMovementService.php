<?php

namespace App\Services\InventoryMovement;

use App\Schemas\InventoryMovement\InventoryMovementQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\InventoryMovement;

class InventoryMovementService implements InventoryMovementServiceInterface
{
    public function findAll(InventoryMovementQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = InventoryMovement::with([
                'inventory.item',
                'inventory.unit',
                'author'
            ])->orderBy('created_at', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get inventory movements", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage().$e->getLine());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $inventoryMovement = InventoryMovement::with([
                'inventory.item',
                'inventory.unit',
                'author'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$inventoryMovement) {
                return ServiceResponse::notFound("inventory movement not found");
            }
            return ServiceResponse::statusOK("successfully get inventory movement", $inventoryMovement);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
