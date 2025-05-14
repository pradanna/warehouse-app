<?php

namespace App\Services\Item;

use App\Schemas\Item\ItemSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Models\Item;
use App\Schemas\Item\ItemQuery;

class ItemService implements ItemServiceInterface
{
    public function create(ItemSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $data = [
                'category_id' => $schema->getCategoryId(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
            ];
            Item::create($data);
            return ServiceResponse::statusCreated("successfully create item");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(ItemQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Item::with(['category:id,name'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData()->makeHidden(['created_at', 'updated_at', 'category_id']);
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get items", $data, $meta);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $item = Item::with(['category:id,name'])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("item not found");
            }
            $item->makeHidden(['created_at', 'updated_at', 'category_id']);
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
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();

            $item = Item::with(['category:id,name'])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("item not found");
            }

            $data = [
                'category_id' => $schema->getCategoryId(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
            ];

            $item->update($data);
            return ServiceResponse::statusOK("successfully update item");
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
