<?php

namespace App\Services\WarehouseExpense;

use App\Commons\Http\ServiceResponse;
use App\Schemas\WarehouseExpense\WarehouseExpenseQuery;
use App\Schemas\WarehouseExpense\WarehouseExpenseSchema;

interface WarehouseExpenseServiceInterface
{
    public function create(WarehouseExpenseSchema $schema): ServiceResponse;
    public function findAll(WarehouseExpenseQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, WarehouseExpenseSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
