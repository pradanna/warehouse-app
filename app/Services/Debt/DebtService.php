<?php

namespace App\Services\Debt;

use App\Commons\Enum\PurchasePaymentStatus;
use App\Commons\Enum\PurchasePaymentType;
use App\Schemas\Debt\DebtQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\Debt;
use Illuminate\Database\Eloquent\Builder;

class DebtService implements DebtServiceInterface
{
    public function findAll(DebtQuery $queryParams): ServiceResponse
    {
        try {
            $data = $this->debtQuery($queryParams)
                ->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get debts", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $debt = Debt::with([
                'purchase.supplier'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$debt) {
                return ServiceResponse::notFound("debt not found");
            }
            return ServiceResponse::statusOK("successfully get debt", $debt);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function summary(DebtQuery $queryParams): ServiceResponse
    {
        try {
            $total = $this->debtQuery($queryParams)
                ->sum('amount_rest');
            return ServiceResponse::statusOK("successfully get debt summary", $total);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    private function debtQuery(DebtQuery $queryParams): Builder
    {
        $queryParams->hydrateQuery();
        return Debt::with([
            'purchase.supplier',
        ])
            ->when($queryParams->getSupplierId(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->whereRelation('purchase', 'supplier_id', '=', $queryParams->getSupplierId());
            })
            ->when($queryParams->getStatus(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                if ($queryParams->getStatus() === PurchasePaymentStatus::Paid->value) {
                    return $q->where('amount_rest', '<=', 0);
                }
                return $q->where('amount_rest', '>', 0);
            })
            ->orderBy('created_at', 'DESC');
    }
}
