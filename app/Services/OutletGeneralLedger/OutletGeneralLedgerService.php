<?php

namespace App\Services\OutletGeneralLedger;

use App\Commons\Enum\CashFlowReferenceType;
use App\Schemas\OutletGeneralLedger\OutletGeneralLedgerQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\OutletIncome;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class OutletGeneralLedgerService implements OutletGeneralLedgerServiceInterface
{
    public function findAll(OutletGeneralLedgerQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $incomes = OutletIncome::with(['outlet', 'author'])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->orderBy('date', 'ASC')
                ->get();

            $purchases = CashFlow::with([])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->where('reference_type', '=', CashFlowReferenceType::Purchase->value)
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->orderBy('date', 'ASC')
                ->get();
            $startDate = Carbon::createFromDate($queryParams->getYear(), $queryParams->getMonth(), 1)->startOfMonth();
            $endDate = Carbon::createFromDate($queryParams->getYear(), $queryParams->getMonth(), 1)->endOfMonth();
            $period = CarbonPeriod::create($startDate, $endDate);
            $data = [];
            foreach ($period as $date) {
                $periodDate = $date->toDateString();
                $cash = $incomes->where('date', '=', $periodDate)->sum('cash');
                $digital = $incomes->where('date', '=', $periodDate)->sum('digital');
                $total = ($cash + $digital);
                $purchase = $purchases->where('date', '=', $periodDate)->sum('amount');
                array_push($data, [
                    'date' => $periodDate,
                    'income' => [
                        'cash' => $cash,
                        'digital' => $digital,
                        'total' => $total
                    ],
                    'purchase' => $purchase
                ]);
            }
            return ServiceResponse::statusOK("successfully get outlet general ledgers", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
