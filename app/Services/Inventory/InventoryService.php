<?php

namespace App\Services\Inventory;

use App\Commons\Http\HttpStatus;
use App\Schemas\Inventory\InventorySchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Http\Resources\Inventory\InventoryResource;
use App\Models\Inventory;
use App\Models\Item;
use App\Schemas\Inventory\InventoryQuery;
use Illuminate\Contracts\Support\Responsable;

class InventoryService implements InventoryServiceInterface
{
    public function create(InventorySchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new InventoryResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $data = [
                'item_id' => $schema->getItemId(),
                'unit_id' => $schema->getUnitId(),
                'sku' => $schema->getSku(),
                'description' => $schema->getDescription(),
                'price' => $schema->getPrice(),
                'current_stock' => $schema->getCurrentStock(),
                'min_stock' => $schema->getMinStock(),
                'max_stock' => $schema->getMaxStock()
            ];
            Inventory::create($data);
            return (new InventoryResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create inventory");
        } catch (\Throwable $e) {
            return (new InventoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(InventoryQuery $queryParams): Responsable
    {
        try {
            $queryParams->hydrateQuery();
            $query = Inventory::with(['item', 'unit'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereRelation('item', 'name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy(
                    Item::select('name')
                        ->whereColumn('item_id', 'items.id')
                );
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return (new InventoryCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved inventories');
        } catch (\Throwable $e) {
            return (new InventoryResource(null))
                ->withStatus(HttpStatus::InternalServerError)
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
    {
        try {
            $inventory = Inventory::with(['item', 'unit'])
                ->where('id', '=', $id)
                ->first();
            if (!$inventory) {
                return (new InventoryResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("inventory not found");
            }
            return (new InventoryResource($inventory))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved inventory");
        } catch (\Throwable $e) {
            return (new InventoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function patch($id, InventorySchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new InventoryResource(null))
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();

            $inventory = Inventory::with(['item:id,name', 'unit:id,name'])
                ->where('id', '=', $id)
                ->first();
            if (!$inventory) {
                return (new InventoryResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("inventory not found");
            }

            $data = [
                'item_id' => $schema->getItemId(),
                'unit_id' => $schema->getUnitId(),
                'sku' => $schema->getSku(),
                'description' => $schema->getDescription(),
                'price' => $schema->getPrice(),
                'current_stock' => $schema->getCurrentStock(),
                'min_stock' => $schema->getMinStock(),
                'max_stock' => $schema->getMaxStock()
            ];
            $inventory->update($data);
            return (new InventoryResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully update inventory");
        } catch (\Throwable $e) {
            return (new InventoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function delete($id): Responsable
    {
        try {
            Inventory::destroy($id);
            return (new InventoryResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully delete inventory");
        } catch (\Throwable $e) {
            return (new InventoryResource(null))
                ->withMessage($e->getMessage());
        }
    }
}
