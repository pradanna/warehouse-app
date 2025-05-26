<?php

namespace App\Services\PurchasePayment;

use App\Commons\File\FileUploadService;
use App\Schemas\PurchasePayment\PurchasePaymentQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Schemas\PurchasePayment\PurchasePaymentEvidenceSchema;
use App\Schemas\PurchasePayment\PurchasePaymentSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PurchasePaymentService implements PurchasePaymentServiceInterface
{
    public function create(PurchasePaymentSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'purchase_id' => $schema->getPurchaseId(),
                'date' => $schema->getDate(),
                'payment_type' => $schema->getPaymentType(),
                'amount' => $schema->getAmount(),
                'description' => $schema->getDescription(),
                'author_id' => Auth::user()->id
            ];
            $purchasePayment = PurchasePayment::create($data);
            $purchasePayment->load(['purchase', 'author']);
            return ServiceResponse::statusCreated("successfully create purchase payment", $purchasePayment);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
    public function findAll(PurchasePaymentQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = PurchasePayment::with([
                'purchase.supplier',
                'author'
            ])
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->when($queryParams->getSupplierId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereRelation('purchase', 'supplier_id', '=', $queryParams->getSupplierId());
                })
                ->orderBy('created_at', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get purchase payments", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $purchasePayment = PurchasePayment::with([
                'purchase.supplier',
                'author'
            ])->where('id', '=', $id)
                ->first();
            if (!$purchasePayment) {
                return ServiceResponse::notFound("purchase payment not found");
            }
            return ServiceResponse::statusOK("successfully get purchase payment", $purchasePayment);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function uploadEvidence($id, PurchasePaymentEvidenceSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $purchasePayment = PurchasePayment::with([
                'purchase.supplier',
                'author'
            ])->where('id', '=', $id)
                ->first();
            if (!$purchasePayment) {
                return ServiceResponse::notFound("purchase payment not found");
            }
            $fileUploadService = new FileUploadService($schema->getEvidence(), "evidences/purchase");
            $fileUploadResponse = $fileUploadService->upload();
            if (!$fileUploadResponse->isSuccess()) {
                return ServiceResponse::internalServerError('failed to upload file');
            }
            $dataUpdate = [
                'evidence' => $fileUploadResponse->getFileName()
            ];
            $purchasePayment->update($dataUpdate);
            return ServiceResponse::statusOK("successfully get purchase payment", $purchasePayment);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
