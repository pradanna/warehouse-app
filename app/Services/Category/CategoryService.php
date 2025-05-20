<?php

namespace App\Services\Category;

use App\Commons\Http\HttpStatus;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;

class CategoryService implements CategoryServiceInterface
{
    public function create(CategorySchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new CategoryResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'description' => $schema->getDescription()
            ];
            Category::create($data);
            return (new CategoryResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create category");
        } catch (\Throwable $e) {
            return (new CategoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(CategoryQuery $queryParams): Responsable
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
            return (new CategoryCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved categories');
        } catch (\Throwable $e) {
            return (new CategoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
    {
        try {
            $category = Category::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$category) {
                return (new CategoryResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("category not found");
            }
            return (new CategoryResource($category))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved category");
        } catch (\Throwable $e) {
            return (new CategoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function patch($id, CategorySchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new CategoryResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
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
                return (new CategoryResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("category not found");
            }
            $category->update($data);
            return (new CategoryResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully update category");
        } catch (\Throwable $e) {
            return (new CategoryResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function delete($id): Responsable
    {
        try {
            Category::destroy($id);
            return (new CategoryResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully delete category");
        } catch (\Throwable $e) {
            return (new CategoryResource(null))
                ->withMessage($e->getMessage());
        }
    }
}
