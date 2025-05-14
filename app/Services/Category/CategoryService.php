<?php

namespace App\Services\Category;

use App\Commons\Http\HttpStatus;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryService implements CategoryServiceInterface
{
    public function create(CategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'description' => $schema->getDescription()
            ];
            Category::create($data);
            return ServiceResponse::statusCreated("successfully create category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(CategoryQuery $queryParams): JsonResource
    {
        try {
            $queryParams->hydrateQuery();
            $query = Category::with(['items'])
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
            // return CategoryResource::collection($data);
            return (new CategoryResource($data))->withMessage('successfully get categories')->withStatus(HttpStatus::OK);
            // return ServiceResponse::statusOK("successfully get categories", CategoryResource::collection($data), $meta);
        } catch (\Throwable $e) {
            return new CategoryResource(null);
            // return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): JsonResource
    {
        try {
            $category = Category::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$category) {
                return new CategoryResource(null);
            }
            // return ServiceResponse::statusOK("successfully get category", $category);
            return new CategoryResource($category);
        } catch (\Throwable $e) {
            return new CategoryResource(null);
            // return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, CategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();

            $data = [
                'name' => $schema->getName(),
                'description' => $schema->getDescription()
            ];

            $category = Category::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$category) {
                return ServiceResponse::notFound("category not found");
            }
            $category->update($data);
            return ServiceResponse::statusOK("successfully update category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Category::destroy($id);
            return ServiceResponse::statusOK("successfully delete category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
