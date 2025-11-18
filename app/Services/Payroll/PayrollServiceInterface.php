<?php

namespace App\Services\Payroll;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Payroll\PayrollQuery;
use App\Schemas\Payroll\PayrollSchema;

interface PayrollServiceInterface
{
    public function create(PayrollSchema $schema): ServiceResponse;
    public function findAll(PayrollQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function update($id, PayrollSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
