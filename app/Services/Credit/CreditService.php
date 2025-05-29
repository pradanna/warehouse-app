<?php

namespace App\Services\Credit;

use App\Commons\Enum\SalePaymentStatus;
use App\Schemas\Credit\CreditQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\Credit;
use Illuminate\Database\Eloquent\Builder;

class CreditService implements CreditServiceInterface
{
    public function findAll(CreditQuery $queryParams): ServiceResponse
    {
        try {
            $data = $this->creditQuery($queryParams)
                ->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get credits", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $credit = Credit::with([
                'sale.outlet'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$credit) {
                return ServiceResponse::notFound("credit not found");
            }
            return ServiceResponse::statusOK("successfully get credit", $credit);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function summary(CreditQuery $queryParams): ServiceResponse
    {
        try {
            $total = $this->creditQuery($queryParams)
                ->sum('amount_rest');
            return ServiceResponse::statusOK("successfully get credit summary", $total);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    private function creditQuery(CreditQuery $queryParams): Builder
    {
        $queryParams->hydrateQuery();
        return Credit::with([
            'sale.outlet',
        ])
            ->when($queryParams->getOutletId(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->whereRelation('sale', 'outlet_id', '=', $queryParams->getOutletId());
            })
            ->when($queryParams->getStatus(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                if ($queryParams->getStatus() === SalePaymentStatus::Paid->value) {
                    return $q->where('amount_rest', '<=', 0);
                }
                return $q->where('amount_rest', '>', 0);
            })
            ->orderBy('created_at', 'DESC');
    }
}
