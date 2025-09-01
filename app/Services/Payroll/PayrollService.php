<?php

namespace App\Services\Payroll;

use App\Commons\Const\App;
use App\Commons\Enum\CashFlowReferenceType;
use App\Commons\Enum\CashFlowType;
use App\Schemas\Payroll\PayrollSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\OutletExpense;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Schemas\Payroll\PayrollQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollService implements PayrollServiceInterface
{
    public function create(PayrollSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $items = $schema->getItems();

            $itemCollections = collect($items);

            $total = $itemCollections->sum('amount');

            # create cash flow
            # cash flow use digital payment
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Credit->value,
                'name' => "Gaji Pegawai",
                'cash' => 0,
                'digital' => $total,
                'amount' => $total,
                'reference_type' => CashFlowReferenceType::OutletExpense->value,
                'author_id' => $userId,
            ];
            $cashFlow = CashFlow::create($dataCashFlow);

            # create outlet expense
            $dataOutletExpense = [
                'outlet_id' => $schema->getOutletId(),
                'cash_flow_id' => $cashFlow->id,
                'expense_category_id' => App::SalaryExpense,
                'date' => $schema->getDate(),
                'cash' => 0,
                'digital' => $total,
                'amount' => $total,
                'description' => "Gaji Pegawai",
                'author_id' => $userId,
            ];

            $outletExpense = OutletExpense::create($dataOutletExpense);

            $data = [
                'outlet_id' => $schema->getOutletId(),
                'outlet_expense_id' => $outletExpense->id,
                'date' => $schema->getDate(),
                'amount' => $total,
            ];

            $payroll = Payroll::create($data);

            foreach ($items as $item) {
                $dataItem = [
                    'payroll_id' => $payroll->id,
                    'employee_id' => $item['employee_id'],
                    'amount' => $item['amount']
                ];
                PayrollItem::create($dataItem);
            }
            DB::commit();
            return ServiceResponse::statusCreated("successfully create payroll");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(PayrollQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Payroll::with(['items.employee', 'outlet', 'outlet_expense.cash_flow'])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->orderBy('date', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get outlet payrolls", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $payroll = Payroll::with(['items.employee', 'outlet', 'outlet_expense.cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$payroll) {
                return ServiceResponse::notFound("payroll not found");
            }
            return ServiceResponse::statusOK("successfully get payroll", $payroll);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function update($id, PayrollSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $payroll = Payroll::with(['items.employee', 'outlet', 'outlet_expense.cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$payroll) {
                return ServiceResponse::notFound("payroll not found");
            }

            $outletExpense = $payroll->outlet_expense;
            $cashFlow = $outletExpense->cash_flow;

            $items = $schema->getItems();

            $itemCollections = collect($items);

            $total = $itemCollections->sum('amount');

            # update outlet expense
            $dataOutletExpense = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'cash' => 0,
                'digital' => $total,
                'amount' => $total,
                'author_id' => $userId,
            ];
            $outletExpense->update($dataOutletExpense);

            # update cash flow
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'cash' => 0,
                'digital' => $total,
                'amount' => $total,
                'author_id' => $userId,
            ];

            $cashFlow->update($dataCashFlow);

            $data = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'amount' => $total,
            ];

            foreach ($payroll->items as $item) {
                $item->delete();
            }
            $payroll->update($data);
            foreach ($items as $item) {
                $dataItem = [
                    'payroll_id' => $payroll->id,
                    'employee_id' => $item['employee_id'],
                    'amount' => $item['amount']
                ];
                PayrollItem::create($dataItem);
            }
            DB::commit();
            return ServiceResponse::statusOK("successfully update payroll");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse {
        try {
            DB::beginTransaction();
            $payroll = Payroll::with(['items.employee', 'outlet', 'outlet_expense.cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$payroll) {
                return ServiceResponse::notFound("payroll not found");
            }

            $outletExpense = $payroll->outlet_expense;
            $cashFlow = $outletExpense->cash_flow;

            # delete payroll
            foreach ($payroll->items as $item) {
                $item->delete();
            }
            $payroll->delete();

            # delete outlet expense
            $outletExpense->delete();

            # delete cash flow
            $cashFlow->delete();

            DB::commit();
            return ServiceResponse::statusOK("successfully delete payroll");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
