<?php

namespace App\Services\Category;

use App\Commons\Http\HttpStatus;
use App\Commons\Http\ServiceResponse;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;

class CategoryService implements CategoryServiceInterface
{
    public function create(CategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'description' => $schema->getDescription()
            ];
            $category = Category::create($data);
            return ServiceResponse::statusCreated("successfully create category", $category);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(CategoryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Category::with(['items'])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get categories", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $category = Category::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$category) {
                return ServiceResponse::notFound("category not found");
            }
            return ServiceResponse::statusOK("successfully get category", $category);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, CategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
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
            return ServiceResponse::statusOK("successfully update category", $category);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Category::destroy($id);
            return ServiceResponse::statusOK("category delete category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
