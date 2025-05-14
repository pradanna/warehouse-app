<?php

namespace App\Services\Supplier;

use App\Schemas\Supplier\SupplierSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Models\Supplier;
use App\Schemas\Supplier\SupplierQuery;

class SupplierService implements SupplierServiceInterface
{
    public function create(SupplierSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            Supplier::create($data);
            return ServiceResponse::statusCreated("successfully create supplier");
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
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData()->makeHidden(['created_at', 'updated_at']);
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get supplier", $data, $meta);
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
            $supplier->makeHidden(['created_at', 'updated_at']);
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
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
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
            return ServiceResponse::statusOK("successfully update supplier");
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
