<?php

namespace App\Services\Employee;

use App\Schemas\Employee\EmployeeSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\Employee;
use App\Schemas\Employee\EmployeeQuery;

class EmployeeService implements EmployeeServiceInterface
{
    public function create(EmployeeSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
            ];
            Employee::create($data);
            return ServiceResponse::statusCreated("successfully create employee");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(EmployeeQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Employee::with([])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get employees", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $employee = Employee::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$employee) {
                return ServiceResponse::notFound("employee not found");
            }
            return ServiceResponse::statusOK("successfully get employee", $employee);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, EmployeeSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $data = [
                'name' => $schema->getName(),
            ];
            $employee = Employee::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$employee) {
                return ServiceResponse::notFound("employee not found");
            }
            $employee->update($data);
            return ServiceResponse::statusOK("successfully update employee", $employee);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Employee::destroy($id);
            return ServiceResponse::statusOK("successfully delete employee");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
