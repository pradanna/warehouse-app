<?php

namespace App\Services\CashFlow;

use App\Schemas\CashFlow\CashFlowQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;

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
}
