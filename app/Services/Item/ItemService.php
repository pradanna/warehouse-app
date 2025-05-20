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
    public function create(ItemSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new ItemResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $data = [
                'category_id' => $schema->getCategoryId(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
            ];
            Item::create($data);
            return (new ItemResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create item");
        } catch (\Throwable $e) {
            return (new ItemResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(ItemQuery $queryParams): Responsable
    {
        try {
            $queryParams->hydrateQuery();
            $query = Item::with(['category'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return (new ItemCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved items');
        } catch (\Throwable $e) {
            return (new ItemResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
    {
        try {
            $item = Item::with(['category'])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return (new ItemResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("item not found");
            }
            return (new ItemResource($item))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved item");
        } catch (\Throwable $e) {
            return (new ItemResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function patch($id, ItemSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new ItemResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();

            $item = Item::with(['category:id,name'])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return (new ItemResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("item not found");
            }
            $data = [
                'category_id' => $schema->getCategoryId(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
            ];

            $item->update($data);
            return (new ItemResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully update item");
        } catch (\Throwable $e) {
            return (new ItemResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function delete($id): Responsable
    {
        try {
            Item::destroy($id);
            return (new ItemResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully delete item");
        } catch (\Throwable $e) {
            return (new ItemResource(null))
                ->withMessage($e->getMessage());
        }
    }
}
