<?php

namespace App\Services\Inventory;

use App\Schemas\Inventory\InventorySchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Models\Inventory;
use App\Models\Item;
use App\Schemas\Inventory\InventoryQuery;

class InventoryService implements InventoryServiceInterface
{
    public function create(InventorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
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
            return ServiceResponse::statusCreated("successfully create inventory");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(InventoryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Inventory::with(['item:id,name', 'unit:id,name'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereRelation('item', 'name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy(
                    Item::select('name')
                        ->whereColumn('item_id', 'items.id')
                );
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData()->makeHidden([
                'created_at',
                'updated_at',
                'item_id',
                'unit_id'
            ]);
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get inventories", $data, $meta);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $stock = Inventory::with(['item:id,name', 'unit:id,name'])
                ->where('id', '=', $id)
                ->first();
            if (!$stock) {
                return ServiceResponse::notFound("stock not found");
            }
            $stock->makeHidden([
                'created_at',
                'updated_at',
                'item_id',
                'unit_id'
            ]);
            return ServiceResponse::statusOK("successfully get inventory", $stock);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, InventorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();

            $inventory = Inventory::with(['item:id,name', 'unit:id,name'])
                ->where('id', '=', $id)
                ->first();
            if (!$inventory) {
                return ServiceResponse::notFound("inventory not found");
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
            return ServiceResponse::statusOK("successfully update inventory");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Inventory::destroy($id);
            return ServiceResponse::statusOK("successfully delete inventory");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
