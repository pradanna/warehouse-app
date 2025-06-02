<?php

namespace App\Services\SalePayment;

use App\Commons\Enum\SalePaymentStatus;
use App\Commons\Enum\SalePaymentType;
use App\Commons\File\FileUploadService;
use App\Schemas\SalePayment\SalePaymentSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\Credit;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Schemas\SalePayment\SalePaymentEvidenceSchema;
use App\Schemas\SalePayment\SalePaymentQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalePaymentService implements SalePaymentServiceInterface
{
    public function create(SalePaymentSchema $schema): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $sale = Sale::with([])
                ->where('id', '=', $schema->getSaleId())
                ->first();
            if (!$sale) {
                return ServiceResponse::notFound("sale not found");
            }

            if ($sale->payment_type === SalePaymentType::Cash->value) {
                return ServiceResponse::badRequest("cannot create payment for cash sales");
            }

            $credit = Credit::with([])
                ->where('sale_id', '=', $schema->getSaleId())
                ->first();
            if (!$credit) {
                return ServiceResponse::notFound("credit not found");
            }

            $creditAmountRest = $credit->amount_rest;
            if ($creditAmountRest <= 0) {
                return ServiceResponse::badRequest("The credit has been fully paid. No remaining balance.");
            }

            if ($schema->getAmount() > $creditAmountRest) {
                return ServiceResponse::badRequest("Payment exceeds the remaining balance. Please check the amount entered.");
            }


            $data = [
                'sale_id' => $schema->getSaleId(),
                'date' => $schema->getDate(),
                'payment_type' => $schema->getPaymentType(),
                'amount' => $schema->getAmount(),
                'description' => $schema->getDescription(),
                'author_id' => Auth::user()->id
            ];
            $salePayment = SalePayment::create($data);

            $newCreditAmountRest = $creditAmountRest - $schema->getAmount();
            $newCreditAmountPaid = $credit->amount_paid + $schema->getAmount();

            $dataCredit = [
                'amount_rest' => $newCreditAmountRest,
                'amount_paid' => $newCreditAmountPaid
            ];
            $credit->update($dataCredit);

            if ($newCreditAmountRest <= 0) {
                $sale->update(['payment_status' => SalePaymentStatus::Paid->value]);
            } else {
                $sale->update(['payment_status' => SalePaymentStatus::Partial->value]);
            }

            $salePayment->load(['sale.credit', 'author']);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create sale payment", $salePayment);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(SalePaymentQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = SalePayment::with([
                'sale.outlet',
                'author'
            ])
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->when($queryParams->getOutletId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereRelation('sale', 'outlet_id', '=', $queryParams->getOutletId());
                })
                ->orderBy('created_at', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get sale payments", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $salePayment = SalePayment::with([
                'sale.outlet',
                'author'
            ])->where('id', '=', $id)
                ->first();
            if (!$salePayment) {
                return ServiceResponse::notFound("sale payment not found");
            }
            return ServiceResponse::statusOK("successfully get sale payment", $salePayment);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function uploadEvidence($id, SalePaymentEvidenceSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $salePayment = SalePayment::with([
                'sale.outlet',
                'author'
            ])->where('id', '=', $id)
                ->first();
            if (!$salePayment) {
                return ServiceResponse::notFound("sale payment not found");
            }
            $fileUploadService = new FileUploadService($schema->getEvidence(), "evidences/sale");
            $fileUploadResponse = $fileUploadService->upload();
            if (!$fileUploadResponse->isSuccess()) {
                return ServiceResponse::internalServerError('failed to upload file');
            }
            $dataUpdate = [
                'evidence' => $fileUploadResponse->getFileName()
            ];
            $salePayment->update($dataUpdate);
            return ServiceResponse::statusCreated("successfully upload sale payment evidence", $salePayment);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
