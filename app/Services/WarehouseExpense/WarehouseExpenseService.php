<?php

namespace App\Services\WarehouseExpense;

use App\Schemas\WarehouseExpense\WarehouseExpenseQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\ExpenseCategory;
use App\Models\WarehouseExpense;
use App\Schemas\WarehouseExpense\WarehouseExpenseSchema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseExpenseService implements WarehouseExpenseServiceInterface
{
    public function findAll(WarehouseExpenseQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = WarehouseExpense::with(['author', 'expense_category'])
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->when($queryParams->getExpenseCategoryId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('expense_category_id', '=', $queryParams->getExpenseCategoryId());
                })
                ->orderBy('date', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get warehouse expense", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $warehouseExpense = WarehouseExpense::with(['author', 'expense_category'])
                ->where('id', '=', $id)
                ->first();
            if (!$warehouseExpense) {
                return ServiceResponse::notFound("warehouse expense not found");
            }
            return ServiceResponse::statusOK("successfully get warehouse expense", $warehouseExpense);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function create(WarehouseExpenseSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $expenseCategory = ExpenseCategory::with([])
                ->where('id', '=', $schema->getExpenseCategoryId())
                ->first();
            if (!$expenseCategory) {
                return ServiceResponse::notFound("expense category not found");
            }

            #create warehouse expense
            $dataExpense = [
                'expense_category_id' => $schema->getExpenseCategoryId(),
                'date' => $schema->getDate(),
                'amount' => $schema->getAmount(),
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];

            WarehouseExpense::create($dataExpense);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create warehouse expense");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, WarehouseExpenseSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $warehouseExpense = WarehouseExpense::with(['author', 'expense_category'])
                ->where('id', '=', $id)
                ->first();
            if (!$warehouseExpense) {
                return ServiceResponse::notFound("warehouse warehouse not found");
            }

            $expenseCategory = ExpenseCategory::with([])
                ->where('id', '=', $schema->getExpenseCategoryId())
                ->first();
            if (!$expenseCategory) {
                return ServiceResponse::notFound("expense category not found");
            }

            #create warehouse expense
            $dataExpense = [
                'expense_category_id' => $schema->getExpenseCategoryId(),
                'date' => $schema->getDate(),
                'amount' => $schema->getAmount(),
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];

            $warehouseExpense->update($dataExpense);
            DB::commit();
            return ServiceResponse::statusOK("successfully update warehouse expense");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse {
         try {
            DB::beginTransaction();
            $warehouseExpense = WarehouseExpense::with(['author', 'expense_category'])
                ->where('id', '=', $id)
                ->first();
            if (!$warehouseExpense) {
                return ServiceResponse::notFound("warehouse warehouse not found");
            }
            #delete warhouse expense
            $warehouseExpense->delete();
            DB::commit();
            return ServiceResponse::statusOK("successfully delete outlet expense");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
