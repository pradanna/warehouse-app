<?php

namespace App\Services\Item;

use App\Commons\Http\HttpStatus;
use App\Schemas\Item\ItemSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Http\Resources\Item\ItemCollection;
use App\Http\Resources\Item\ItemResource;
use App\Models\Item;
use App\Schemas\Item\ItemQuery;
use Illuminate\Contracts\Support\Responsable;

class ItemService implements ItemServiceInterface
{
    public function create(ItemSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'category_id' => $schema->getCategoryId(),
                'material_category_id' => $schema->getMaterialCategoryId(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
            ];
            $item = Item::create($data);
            $item->load(['category', 'material_category']);
            return ServiceResponse::statusCreated("successfully create item", $item);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(ItemQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Item::with(['category', 'material_category'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get items", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $item = Item::with(['category', 'material_category'])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("item not found");
            }
            return ServiceResponse::statusOK("successfully get item", $item);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, ItemSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $item = Item::with(['category:id,name', 'material_category'])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("item not found");
            }
            $data = [
                'category_id' => $schema->getCategoryId(),
                'material_category_id' => $schema->getMaterialCategoryId(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
            ];

            $item->update($data);
            $item->load(['category', 'material_category']);
            return ServiceResponse::statusOK("successfully update item", $item);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Item::destroy($id);
            return ServiceResponse::statusOK("successfully delete item");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
