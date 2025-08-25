<?php

namespace App\Services\OutletPurchase;

use App\Commons\Enum\CashFlowReferenceType;
use App\Commons\Enum\CashFlowType;
use App\Commons\Http\ServiceResponse;
use App\Commons\Schema\BaseSchema;
use App\Models\CashFlow;
use App\Models\OutletPurchase;
use App\Models\Sale;
use App\Schemas\OutletPurchase\OutletPurchaseQuery;
use App\Schemas\OutletPurchase\OutletPurchaseSchema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutletPurchaseService implements OutletPurchaseServiceInterface
{
    public function findAll(OutletPurchaseQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $data = OutletPurchase::with(['sale', 'cash_flow', 'outlet'])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->orderBy('date', 'ASC')
                ->get();
            return ServiceResponse::statusOK("successfully get outlet purchases", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $outletPurchase = OutletPurchase::with(['sale', 'cash_flow', 'outlet'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletPurchase) {
                return ServiceResponse::notFound("outlet purchase not found");
            }
            return ServiceResponse::statusOK("successfully get outlet purchase", $outletPurchase);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function create(OutletPurchaseSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $cashFlowBody = $schema->getCashFlow();

            $sale = Sale::with([])
                ->where('id', '=', $schema->getSaleId())
                ->first();

            if (!$sale) {
                return ServiceResponse::notFound("sale info not found");
            }

            $arrAmmount = $schema->getAmount();
            $total = $arrAmmount['cash'] + $arrAmmount['digital'];
            $outletId = $sale->outlet_id;
            $cashFlowData = [
                'outlet_id' => $outletId,
                'date' => $cashFlowBody['date'],
                'type' => CashFlowType::Credit->value,
                'name' => $cashFlowBody['name'],
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
                'reference_type' => CashFlowReferenceType::OutletPurchase->value,
                'author_id' => $userId,
            ];

            $cashFlow = CashFlow::create($cashFlowData);

            $data = [
                'sale_id' => $schema->getSaleId(),
                'cash_flow_id' => $cashFlow->id,
                'outlet_id' => $outletId,
                'date' => $schema->getDate(),
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
            ];

            OutletPurchase::create($data);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create outlet purchase");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function update($id, OutletPurchaseSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $cashFlowBody = $schema->getCashFlow();

            $outletPurchase = OutletPurchase::with(['sale', 'cash_flow', 'outlet'])
                ->where('id', '=', $id)
                ->first();

            if (!$outletPurchase) {
                return ServiceResponse::notFound("outlet purchase not found");
            }

            $sale = Sale::with([])
                ->where('id', '=', $schema->getSaleId())
                ->first();

            if (!$sale) {
                return ServiceResponse::notFound("sale info not found");
            }

            $arrAmmount = $schema->getAmount();
            $total = $arrAmmount['cash'] + $arrAmmount['digital'];

            $cashFlow = $outletPurchase->cash_flow;

            $outletId = $sale->outlet_id;
            $cashFlowData = [
                'outlet_id' => $outletId,
                'date' => $cashFlowBody['date'],
                'type' => CashFlowType::Credit->value,
                'name' => $cashFlowBody['name'],
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
                'reference_type' => CashFlowReferenceType::OutletPurchase->value,
                'author_id' => $userId,
            ];

            $cashFlow->update($cashFlowData);

            $data = [
                'sale_id' => $schema->getSaleId(),
                'outlet_id' => $outletId,
                'date' => $schema->getDate(),
                'cash' => $arrAmmount['cash'],
                'digital' => $arrAmmount['digital'],
                'amount' => $total,
            ];

            $outletPurchase->update($data);
            DB::commit();
            return ServiceResponse::statusOK("successfully update outlet purchase");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $outletPurchase = OutletPurchase::with(['sale', 'cash_flow', 'outlet'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletPurchase) {
                return ServiceResponse::notFound("outlet purchase not found");
            }

            $outletPurchase->cash_flow->delete();
            $outletPurchase->delete();
            DB::commit();
            return ServiceResponse::statusOK("successfully delete outlet purchase");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
