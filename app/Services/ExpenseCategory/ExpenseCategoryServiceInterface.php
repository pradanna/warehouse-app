<?php

namespace App\Services\ExpenseCategory;

use App\Commons\Http\ServiceResponse;
use App\Schemas\ExpenseCategory\ExpenseCategoryQuery;
use App\Schemas\ExpenseCategory\ExpenseCategorySchema;

interface ExpenseCategoryServiceInterface
{
    public function create(ExpenseCategorySchema $schema): ServiceResponse;
    public function findAll(ExpenseCategoryQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, ExpenseCategorySchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
