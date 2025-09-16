<?php

namespace App\Services\FundTransfer;

use App\Commons\Enum\CashFlowType;
use App\Schemas\FundTransfer\FundTransferSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\FundTransfer;
use App\Schemas\FundTransfer\FundTransferQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FundTransferService implements FundTransferServiceInterface
{


    public function findAll(FundTransferQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = FundTransfer::with(['outlet'])
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->when($queryParams->getOutletId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('outlet_id', '=', $queryParams->getOutletId());
                })
                ->when($queryParams->getTransferTo(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('transfer_to', '=', $queryParams->getTransferTo());
                })
                ->orderBy('date', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get fund transfers", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $fundTransfer = FundTransfer::with(['outlet'])
                ->where('id', '=', $id)
                ->first();
            if (!$fundTransfer) {
                return ServiceResponse::notFound("fund transfer not found");
            }
            return ServiceResponse::statusOK("successfully get fundTransfer", $fundTransfer);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function create(FundTransferSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $cashFlowName = match ($schema->getTransferTo()) {
                'digital' => 'Setor Tunai',
                'cash' => 'Tarik Tunai',
                default => '-'
            };

            #create cash flow
            $debitCashFlowData = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Debit->value,
                'name' => $cashFlowName,
                'cash' => $schema->getTransferTo() === 'cash' ? $schema->getAmount() : 0,
                'digital' => $schema->getTransferTo() === 'digital' ? $schema->getAmount() : 0,
                'amount' => $schema->getAmount(),
                'description' => null,
                'reference_type' => null,
                'author_id' => $userId,
            ];
            $debitCashFlow = CashFlow::create($debitCashFlowData);

            $creditCashFlowData = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Credit->value,
                'name' => $cashFlowName,
                'cash' => $schema->getTransferTo() === 'cash' ? 0 : $schema->getAmount(),
                'digital' => $schema->getTransferTo() === 'digital' ? 0 : $schema->getAmount(),
                'amount' => $schema->getAmount(),
                'description' => null,
                'reference_type' => null,
                'author_id' => $userId,
            ];

            $creditCashFlow = CashFlow::create($creditCashFlowData);

            #create fund transfer
            $dataFundTransfer = [
                'outlet_id' => $schema->getOutletId(),
                'debit_cash_flow_id' => $debitCashFlow->id,
                'credit_cash_flow_id' => $creditCashFlow->id,
                'date' => $schema->getDate(),
                'amount' => $schema->getAmount(),
                'transfer_to' => $schema->getTransferTo(),
            ];

            FundTransfer::create($dataFundTransfer);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create fund transfer");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, FundTransferSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $fundTransfer = FundTransfer::with(['outlet', 'credit_cash_flow', 'debit_cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$fundTransfer) {
                return ServiceResponse::notFound("fund transfer not found");
            }

            $cashFlowName = match ($schema->getTransferTo()) {
                'digital' => 'Setor Tunai',
                'cash' => 'Tarik Tunai',
                default => '-'
            };

            $debitCashFlow = $fundTransfer->debit_cash_flow;
            $creditCashFlow = $fundTransfer->credit_cash_flow;

            #update fund transfer
            $dataFundTransfer = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'amount' => $schema->getAmount(),
                'transfer_to' => $schema->getTransferTo(),
            ];

            $fundTransfer->update($dataFundTransfer);

            #update cash flow
            $debitCashFlowData = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Debit->value,
                'name' => $cashFlowName,
                'cash' => $schema->getTransferTo() === 'cash' ? $schema->getAmount() : 0,
                'digital' => $schema->getTransferTo() === 'digital' ? $schema->getAmount() : 0,
                'amount' => $schema->getAmount(),
                'description' => null,
                'reference_type' => null,
                'author_id' => $userId,
            ];
            $debitCashFlow->update($debitCashFlowData);

            $creditCashFlowData = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Credit->value,
                'name' => $cashFlowName,
                'cash' => $schema->getTransferTo() === 'cash' ? 0 : $schema->getAmount(),
                'digital' => $schema->getTransferTo() === 'digital' ? 0 : $schema->getAmount(),
                'amount' => $schema->getAmount(),
                'description' => null,
                'reference_type' => null,
                'author_id' => $userId,
            ];
            $creditCashFlow->update($creditCashFlowData);
            DB::commit();
            return ServiceResponse::statusOK("successfully update fund transfer");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $fundTransfer = FundTransfer::with(['outlet', 'credit_cash_flow', 'debit_cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$fundTransfer) {
                return ServiceResponse::notFound("fund transfer not found");
            }

            $debitCashFlow = $fundTransfer->debit_cash_flow;
            $creditCashFlow = $fundTransfer->credit_cash_flow;

            #delete fund transfer
            $fundTransfer->delete();

            #delete cash flow
            $debitCashFlow->delete();
            $creditCashFlow->delete();
            DB::commit();
            return ServiceResponse::statusOK("successfully delete fund transfer");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
