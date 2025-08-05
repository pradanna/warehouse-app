<?php

namespace App\Services\CashFlow;

use App\Schemas\CashFlow\CashFlowQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\ExpenseCategory;
use App\Models\MaterialCategory;
use App\Models\OutletExpense;
use App\Models\OutletIncome;
use App\Models\Sale;
use App\Schemas\CashFlow\CashFlowSummaryQuery;

class CashFlowService implements CashFlowServiceInterface
{
    public function findAll(CashFlowQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $data = CashFlow::with(['outlet', 'author'])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->when($queryParams->getType(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('type', '=', $queryParams->getType());
                })
                ->orderBy('date', 'ASC')
                ->orderByRaw("FIELD(type, 'debit', 'credit')")
                ->get();
            return ServiceResponse::statusOK("successfully get cash flows", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function summary(CashFlowSummaryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();

            #get data incomes
            $incomes = OutletIncome::with([])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->get();

            #get data outlet purchases
            $purchases = Sale::with(['items.inventory.item'])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->get();

            #get data outlet expenses
            $expenses = OutletExpense::with([])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->get();

            #incomes
            #calculate incomes
            $cashIncomes = $incomes->sum('cash');
            $digitalIncomes = $incomes->sum('digital');
            $totalIncomes = ($cashIncomes + $digitalIncomes);
            #data incomes
            $incomesPayload = [
                [
                    'title' => 'PENDAPATAN CASH',
                    'value' => $cashIncomes,
                    'type' => 'sub'
                ],
                [
                    'title' => 'PENDAPATAN DIGITAL',
                    'value' => $digitalIncomes,
                    'type' => 'sub'
                ],
                [
                    'title' => 'TOTAL KAS MASUK',
                    'value' => $totalIncomes,
                    'type' => 'main'
                ]
            ];

            #get material categories
            $materialCategories = MaterialCategory::with([])
                ->orderBy('created_at', 'ASC')
                ->get();

            #get material expenses
            $purchasesItems = [];
            foreach ($purchases as $purchase) {
                $carts = $purchase->items;
                foreach ($carts as $cart) {
                    $productName = $cart->inventory->item->name;
                    $materialCategoryId = $cart->inventory->item->material_category_id;
                    $total = $cart->total;
                    $purchasesItems[] = [
                        'material_category_id' => $materialCategoryId,
                        'name' => $productName,
                        'total' => $total
                    ];
                }
            }
            $purchasesItemCollection = collect($purchasesItems);

            $materialExpense = [];
            $totalMaterialExpense = 0;
            foreach ($materialCategories as $materialCategory) {
                $materialCategoryId = $materialCategory->id;
                $value = $purchasesItemCollection->where('material_category_id', '=', $materialCategoryId)->sum('total');
                $totalMaterialExpense += $value;
                $materialExpense[] = [
                    'title' => "TOTAL " . strtoupper($materialCategory->name),
                    'value' => $value,
                    'type' => 'sub'
                ];
            }

            $materialExpense[] = [
                'title' => 'TOTAL BAHAN BAKU KESELURUHAN',
                'value' => $totalMaterialExpense,
                'type' => 'main'
            ];


            #get expense category
            $expenseCategories = ExpenseCategory::with([])
                ->orderBy('name', 'ASC')
                ->get();

            #get outlet expense
            $outletExpense = [];
            $totalOutletExpense = 0;
            foreach ($expenseCategories as $expenseCategory) {
                $expenseCategoryId = $expenseCategory->id;
                $value = $expenses->where('expense_category_id', '=', $expenseCategoryId)->sum('amount');
                $totalOutletExpense += $value;
                $outletExpense[] = [
                    'title' => "TOTAL " . strtoupper($expenseCategory->name),
                    'value' => $value,
                    'type' => 'sub'
                ];
            }

            $outletExpense[] = [
                'title' => "TOTAL SELAIN BAHAN BAKU",
                'value' => $totalOutletExpense,
                'type' => 'main'
            ];

            $payload = [
                'debit' => $incomesPayload,
                'credit' => [
                    'material_expense' => $materialExpense,
                    'outlet_expense' => $outletExpense
                ],
                'income' => $totalIncomes,
                'expense' => $totalOutletExpense,
                'revenue' => ($totalIncomes - $totalOutletExpense)
            ];

            return ServiceResponse::statusOK("successfully get cash flow summary", $payload);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
