<?php

namespace App\Services\OutletExpense;

use App\Commons\Http\ServiceResponse;
use App\Schemas\OutletExpense\OutletExpenseQuery;
use App\Schemas\OutletExpense\OutletExpenseSchema;

interface OutletExpenseServiceInterface
{
    public function create(OutletExpenseSchema $schema): ServiceResponse;
    public function findAll(OutletExpenseQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, OutletExpenseSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
