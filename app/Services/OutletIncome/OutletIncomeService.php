<?php

namespace App\Services\OutletIncome;

use App\Commons\Enum\CashFlowReferenceType;
use App\Commons\Enum\CashFlowType;
use App\Schemas\OutletIncome\OutletIncomeSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\CashFlow;
use App\Models\OutletIncome;
use App\Schemas\OutletIncome\OutletIncomeMutationSchema;
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
            $byMutation = 0;

            $incomeExist = OutletIncome::with([])
                ->where('outlet_id', '=', $schema->getOutletId())
                ->where('date', '=', $schema->getDate())
                ->first();
            if ($incomeExist) {
                return ServiceResponse::badRequest("income already exist");
            }
            #create cash flows
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Debit->value,
                'name' => "Omset {$formattedDate}",
                'cash' => $income['cash'],
                'digital' => $income['digital'],
                'amount' => $total,
                'description' => null,
                'reference_type' => CashFlowReferenceType::OutletIncome->value,
                'description' => null,
                'author_id' => $userId,
            ];
            $cashFlow = CashFlow::create($dataCashFlow);

            // $byMutation = $income['by_mutation'];
            $dataIncome = [
                'outlet_id' => $schema->getOutletId(),
                'cash_flow_id' => $cashFlow->id,
                'date' => $schema->getDate(),
                'name' => "Omset {$formattedDate}",
                'cash' => $income['cash'],
                'digital' => $income['digital'],
                'total' => $total,
                'by_mutation' => $byMutation,
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];
            OutletIncome::create($dataIncome);
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

    public function findByID($id): ServiceResponse
    {
        try {
            $outletIncome = OutletIncome::with(['outlet', 'author'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletIncome) {
                return ServiceResponse::notFound("outlet income not found");
            }
            return ServiceResponse::statusOK("successfully get outlet income", $outletIncome);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function update($id, OutletIncomeSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $outletIncome = OutletIncome::with(['outlet', 'author', 'cash_flow'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletIncome) {
                return ServiceResponse::notFound("outlet income not found");
            }

            #create outlet income
            Carbon::setLocale('id');
            $formattedDate = Carbon::parse($schema->getDate())->translatedFormat('d F Y');
            $income = $schema->getIncome();
            $total = $income['cash'] + $income['digital'];
            $byMutation = 0;

            # update data income
            $dataIncome = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'name' => "Omset {$formattedDate}",
                'cash' => $income['cash'],
                'digital' => $income['digital'],
                'total' => $total,
                'by_mutation' => $byMutation,
                'description' => $schema->getDescription(),
                'author_id' => $userId,
            ];
            $outletIncome->update($dataIncome);

            $cashFlow = $outletIncome->cash_flow;
            #create cash flows
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'amount' => $total,
                'author_id' => $userId,
            ];
            $cashFlow->update($dataCashFlow);

            // $byMutation = $income['by_mutation'];

            DB::commit();
            return ServiceResponse::statusOK("successfully update outlet income");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function updateMutation($id, OutletIncomeMutationSchema $schema): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $outletIncome = OutletIncome::with(['outlet', 'author'])
                ->where('id', '=', $id)
                ->first();
            if (!$outletIncome) {
                return ServiceResponse::notFound("outlet income not found");
            }

            $outletIncome->update([
                'by_mutation' => $schema->getAmount(),
                'mutation_date' => $schema->getDate(),
            ]);
            DB::commit();
            return ServiceResponse::statusOK("successfully update by mutation");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
