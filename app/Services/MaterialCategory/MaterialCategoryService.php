<?php

namespace App\Services\MaterialCategory;

use App\Schemas\MaterialCategory\MaterialCategorySchema;
use App\Commons\Http\ServiceResponse;
use App\Models\MaterialCategory;
use App\Schemas\MaterialCategory\MaterialCategoryQuery;

class MaterialCategoryService implements MaterialCategoryServiceInterface
{
    public function create(MaterialCategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
            ];
            MaterialCategory::create($data);
            return ServiceResponse::statusCreated("successfully create material category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(MaterialCategoryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = MaterialCategory::with([])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get material categories", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $materialCategory = MaterialCategory::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$materialCategory) {
                return ServiceResponse::notFound("material category not found");
            }
            return ServiceResponse::statusOK("successfully get material category", $materialCategory);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, MaterialCategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
            ];

            $materialCategory = MaterialCategory::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$materialCategory) {
                return ServiceResponse::notFound("material category not found");
            }
            $materialCategory->update($data);
            return ServiceResponse::statusOK("successfully update material category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            MaterialCategory::destroy($id);
            return ServiceResponse::statusOK("successfully delete material category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
