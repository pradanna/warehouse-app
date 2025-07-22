<?php

namespace App\Services\OutletIncome;

use App\Commons\Enum\CashFlowType;
use App\Schemas\OutletIncome\OutletIncomeSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\OutletIncome;
use App\Schemas\OutletIncome\OutletIncomeQuery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutletIncomeService implements OutletIncomeServiceInterface
{
    public function create(OutletIncomeSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            #create outlet income
            Carbon::setLocale('id');
            $formattedDate = Carbon::parse($schema->getDate())->translatedFormat('d F Y');
            $income = $schema->getIncome();
            $total = $income['cash'] + $income['digital'];
            $dataIncome = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'name' => "Omset {$formattedDate}",
                'cash' => $income['cash'],
                'digital' => $income['digital'],
                'total' => $total,
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];
            OutletIncome::create($dataIncome);

            #create cash flows
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Debit->value,
                'name' => "Omset {$formattedDate}",
                'amount' => $total,
                'description' => null,
                'author_id' => $userId,
            ];
            CashFlow::create($dataCashFlow);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create outlet income");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(OutletIncomeQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $data = OutletIncome::with(['outlet', 'author'])
                ->where('outlet_id', '=', $queryParams->getOutletId())
                ->when(($queryParams->getMonth() && $queryParams->getYear()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereMonth('date', $queryParams->getMonth())
                        ->whereYear('date', $queryParams->getYear());
                })
                ->orderBy('date', 'ASC')
                ->get();
            return ServiceResponse::statusOK("successfully get outlet incomes", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
