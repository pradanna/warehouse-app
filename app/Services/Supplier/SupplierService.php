<?php

namespace App\Services\Supplier;

use App\Commons\Http\HttpStatus;
use App\Commons\Http\ServiceResponse;
use App\Schemas\Supplier\SupplierSchema;
use App\Http\Resources\Supplier\SupplierCollection;
use App\Http\Resources\Supplier\SupplierResource;
use App\Models\Supplier;
use App\Schemas\Supplier\SupplierQuery;
use Illuminate\Contracts\Support\Responsable;

class SupplierService implements SupplierServiceInterface
{
    public function create(SupplierSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            $supplier = Supplier::create($data);
            return ServiceResponse::statusCreated("successfully create supplier", $supplier);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(SupplierQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Supplier::with([])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get suppliers", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $supplier = Supplier::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$supplier) {
                return ServiceResponse::notFound("supplier not found");
            }
            return ServiceResponse::statusOK("successfully get supplier", $supplier);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, SupplierSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $supplier = Supplier::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$supplier) {
                return ServiceResponse::notFound("supplier not found");
            }
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            $supplier->update($data);
            return ServiceResponse::statusOK("successfully update supplier", $supplier);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Supplier::destroy($id);
            return ServiceResponse::statusOK("successfully delete supplier");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
