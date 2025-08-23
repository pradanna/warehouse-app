<?php

namespace App\Services\OutletExpense;

use App\Commons\Enum\CashFlowReferenceType;
use App\Commons\Enum\CashFlowType;
use App\Schemas\OutletExpense\OutletExpenseQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\ExpenseCategory;
use App\Models\OutletExpense;
use App\Schemas\OutletExpense\OutletExpenseSchema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutletExpenseService implements OutletExpenseServiceInterface
{
    public function findAll(OutletExpenseQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = OutletExpense::with(['outlet', 'author', 'expense_category'])
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->when($queryParams->getOutletId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('outlet_id', '=', $queryParams->getOutletId());
                })
                ->when($queryParams->getExpenseCategoryId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('expense_category_id', '=', $queryParams->getExpenseCategoryId());
                })
                ->orderBy('date', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get outlet expense", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $outletExpense = OutletExpense::with(['outlet', 'author', 'expense_category'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletExpense) {
                return ServiceResponse::notFound("outlet expense not found");
            }
            return ServiceResponse::statusOK("successfully get outlet expense", $outletExpense);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function create(OutletExpenseSchema $schema): ServiceResponse
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

            $arrAmmount = $schema->getAmount();
            $total = $arrAmmount['cash'] + $arrAmmount['digital'];

            #create cash flow
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Credit->value,
                'name' => 'Pengeluaran ' . $expenseCategory->name,
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
                'description' => null,
                'reference_type' => CashFlowReferenceType::OutletExpense,
                'author_id' => $userId,
            ];
            $cashFlow = CashFlow::create($dataCashFlow);

            #create outlet expense
            $dataExpense = [
                'outlet_id' => $schema->getOutletId(),
                'cash_flow_id' => $cashFlow->id,
                'expense_category_id' => $schema->getExpenseCategoryId(),
                'date' => $schema->getDate(),
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];

            OutletExpense::create($dataExpense);


            DB::commit();
            return ServiceResponse::statusCreated("successfully create outlet expense");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, OutletExpenseSchema $schema): ServiceResponse
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

            $outletExpense = OutletExpense::with(['cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletExpense) {
                return ServiceResponse::notFound("outlet expense not found");
            }

            $arrAmmount = $schema->getAmount();
            $total = $arrAmmount['cash'] + $arrAmmount['digital'];

            // $currentOutletId = $outletExpense->outlet_id;
            // $currentDate = $outletExpense->date;
            // $currentId = $outletExpense->id;

            #update outlet expense
            $dataExpense = [
                'outlet_id' => $schema->getOutletId(),
                'expense_category_id' => $schema->getExpenseCategoryId(),
                'date' => $schema->getDate(),
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];

            $outletExpense->update($dataExpense);

            #update cash flow
            $cashFlow = $outletExpense->cash_flow;
            if (!$cashFlow) {
                return ServiceResponse::notFound("cash flow not found");
            }
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Credit->value,
                'name' => 'Pengeluaran ' . $expenseCategory->name,
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
                'description' => null,
                'reference_key' => $outletExpense->id,
                'author_id' => $userId,
            ];
            $cashFlow->update($dataCashFlow);
            DB::commit();
            return ServiceResponse::statusOK("successfully update outlet expense");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $outletExpense = OutletExpense::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$outletExpense) {
                return ServiceResponse::notFound("outlet expense not found");
            }

            $currentOutletId = $outletExpense->outlet_id;
            $currentDate = $outletExpense->date;
            $currentId = $outletExpense->id;

            #delete outlet expense
            $outletExpense->delete();

            #update cash flow
            $cashFlow = CashFlow::with([])
                ->where('outlet_id', '=', $currentOutletId)
                ->where('date', '=', $currentDate)
                ->where('reference_key', '=', $currentId)
                ->first();

            if (!$cashFlow) {
                return ServiceResponse::notFound("cash flow not found");
            }
            $cashFlow->delete();
            DB::commit();
            return ServiceResponse::statusOK("successfully delete outlet expense");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
