<?php

namespace App\Services\ExpenseCategory;

use App\Schemas\ExpenseCategory\ExpenseCategoryQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\ExpenseCategory;
use App\Schemas\ExpenseCategory\ExpenseCategorySchema;

class ExpenseCategoryService implements ExpenseCategoryServiceInterface
{
    public function findAll(ExpenseCategoryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = ExpenseCategory::with([])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get expense categories", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $expenseCategory = ExpenseCategory::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$expenseCategory) {
                return ServiceResponse::notFound("expense category not found");
            }
            return ServiceResponse::statusOK("successfully get expense category", $expenseCategory);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function create(ExpenseCategorySchema $schema): ServiceResponse
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
            ExpenseCategory::create($data);
            return ServiceResponse::statusCreated("successfully create expense category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, ExpenseCategorySchema $schema): ServiceResponse
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

            $expenseCategory = ExpenseCategory::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$expenseCategory) {
                return ServiceResponse::notFound("expense category not found");
            }
            $expenseCategory->update($data);
            return ServiceResponse::statusOK("successfully update expense category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            ExpenseCategory::destroy($id);
            return ServiceResponse::statusOK("successfully delete expense category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
