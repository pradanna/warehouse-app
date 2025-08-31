<?php

namespace App\Services\Employee;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Employee\EmployeeQuery;
use App\Schemas\Employee\EmployeeSchema;

interface EmployeeServiceInterface
{
    public function create(EmployeeSchema $schema): ServiceResponse;
    public function findAll(EmployeeQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, EmployeeSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
