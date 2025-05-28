<?php

namespace App\Services\InventoryAdjustment;

use App\Commons\Enum\InventoryMovementType;
use App\Schemas\InventoryAdjustment\InventoryAdjustmentSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryMovement;
use App\Schemas\InventoryAdjustment\InventoryAdjustmentQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentService implements InventoryAdjustmentServiceInterface
{
    public function create(InventoryAdjustmentSchema $schema): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $userId = Auth::user()->id;
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'inventory_id' => $schema->getInventoryId(),
                'date' => $schema->getDate(),
                'quantity' => $schema->getQuantity(),
                'type' => $schema->getType(),
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];

            $inventoryAdjustment = InventoryAdjustment::create($data);

            # update inventory stock
            $inventory = Inventory::with([])
                ->where('id', '=', $schema->getInventoryId())
                ->first();
            if (!$inventory) {
                DB::rollBack();
                return ServiceResponse::notFound("inventory not found");
            }

            $currentStock = $inventory->current_stock;
            $newStock = 0;
            if ($schema->getType() === 'out') {
                $newStock = $currentStock - $schema->getQuantity();
            } else {
                $newStock = $currentStock + $schema->getQuantity();
            }
            $inventory->update(['current_stock' => $newStock]);

            # create inventory movements
            $movementData = [
                'inventory_id' => $schema->getInventoryId(),
                'type' => 'in',
                'quantity_open' => $currentStock,
                'quantity' => $schema->getQuantity(),
                'quantity_close' => $newStock,
                'description' => $schema->getDescription() ? $schema->getDescription() : 'Adjustment',
                'movement_type' => InventoryMovementType::Adjustment->value,
                'movement_reference' => $inventoryAdjustment->id,
                'author_id' => $userId
            ];
            InventoryMovement::create($movementData);
            $inventoryAdjustment->load([
                'inventory.item',
                'inventory.unit',
                'author'
            ]);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create inventory adjustment", $inventoryAdjustment);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(InventoryAdjustmentQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = InventoryAdjustment::with([
                'inventory.item',
                'inventory.unit',
                'author'
            ])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereRelation('inventory.item', 'name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->when($queryParams->getType(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('type', '=', $queryParams->getType());
                })
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->orderBy('created_at', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get inventory adjustments", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $inventoryAdjustment = InventoryAdjustment::with([
                'inventory.item',
                'author'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$inventoryAdjustment) {
                return ServiceResponse::notFound("inventory adjustment not found");
            }
            return ServiceResponse::statusOK("successfully get inventory adjustment", $inventoryAdjustment);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
