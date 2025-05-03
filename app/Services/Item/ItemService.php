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
                'sku' => $schema->getSku(),
                'name' => $schema->getName(),
                'description' => $schema->getDescription(),
                'unit' => $schema->getUnit(),
                'price' =>  $schema->getPrice(),
                'current_stock' => $schema->getCurrentStock(),
                'min_stock' => $schema->getMinStock(),
                'max_stock' => $schema->getMaxStock()
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
            $query = Item::with(['category'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                });
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData();
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get items", $data, $meta);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
