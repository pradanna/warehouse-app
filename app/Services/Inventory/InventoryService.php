<?php

namespace App\Services\Inventory;

use App\Commons\Http\HttpStatus;
use App\Commons\Http\ServiceResponse;
use App\Schemas\Inventory\InventorySchema;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Http\Resources\Inventory\InventoryResource;
use App\Models\Inventory;
use App\Models\Item;
use App\Schemas\Inventory\InventoryQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService implements InventoryServiceInterface
{
    public function create(InventorySchema $schema): ServiceResponse
    {

        DB::beginTransaction();
        try {
            $userId = Auth::user()->id;
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $prices = $schema->getPrices();
            $data = [
                'item_id' => $schema->getItemId(),
                'unit_id' => $schema->getUnitId(),
                'sku' => $schema->getSku(),
                'description' => $schema->getDescription(),
                'current_stock' => 0,
                'min_stock' => $schema->getMinStock(),
                'max_stock' => $schema->getMaxStock(),
                'modified_by' => $userId
            ];
            $inventory = Inventory::create($data);
            $inventory->prices()->createMany($prices);
            $inventory->load(['item', 'unit', 'prices.outlet', 'modifiedBy']);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create inventory", $inventory);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(InventoryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Inventory::with(['item', 'unit', 'prices.outlet', 'modifiedBy'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereRelation('item', 'name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy(
                    Item::select('name')
                        ->whereColumn('item_id', 'items.id')
                );
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get inventories", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $inventory = Inventory::with(['item', 'unit', 'prices.outlet', 'modifiedBy'])
                ->where('id', '=', $id)
                ->first();
            if (!$inventory) {
                return ServiceResponse::notFound("inventory not found");
            }
            return ServiceResponse::statusOK("successfully get inventory", $inventory);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, InventorySchema $schema): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $userId = Auth::user()->id;
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $prices = $schema->getPrices();
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
                'min_stock' => $schema->getMinStock(),
                'max_stock' => $schema->getMaxStock(),
                'modified_by' => $userId
            ];
            $inventory->update($data);
            $inventory->prices()->delete();
            $inventory->prices()->createMany($prices);
            $inventory->load(['item', 'unit', 'prices.outlet', 'modifiedBy']);
            DB::commit();
            return ServiceResponse::statusOK("successfully update inventory", $inventory);
        } catch (\Throwable $e) {
            DB::rollBack();
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
