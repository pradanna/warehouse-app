<?php

namespace App\Services\OutletIncome;

use App\Commons\Http\ServiceResponse;
use App\Schemas\OutletIncome\OutletIncomeMutationSchema;
use App\Schemas\OutletIncome\OutletIncomeQuery;
use App\Schemas\OutletIncome\OutletIncomeSchema;

interface OutletIncomeServiceInterface
{
    public function create(OutletIncomeSchema $schema): ServiceResponse;
    public function findAll(OutletIncomeQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function update($id, OutletIncomeSchema $schema): ServiceResponse;
    public function updateMutation($id, OutletIncomeMutationSchema $schema): ServiceResponse;
}
