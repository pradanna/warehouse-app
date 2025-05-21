<?php

namespace App\Services\Supplier;

use App\Commons\Http\HttpStatus;
use App\Schemas\Supplier\SupplierSchema;
use App\Http\Resources\Supplier\SupplierCollection;
use App\Http\Resources\Supplier\SupplierResource;
use App\Models\Supplier;
use App\Schemas\Supplier\SupplierQuery;
use Illuminate\Contracts\Support\Responsable;

class SupplierService implements SupplierServiceInterface
{
    public function create(SupplierSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new SupplierResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            Supplier::create($data);
            return (new SupplierResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create supplier");
        } catch (\Throwable $e) {
            return (new SupplierResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(SupplierQuery $queryParams): Responsable
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
            return (new SupplierCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved suppliers');
        } catch (\Throwable $e) {
            return (new SupplierResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
    {
        try {
            $supplier = Supplier::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$supplier) {
                return (new SupplierResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("supplier not found");
            }
            return (new SupplierResource($supplier))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved supplier");
        } catch (\Throwable $e) {
            return (new SupplierResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function patch($id, SupplierSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new SupplierResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $supplier = Supplier::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$supplier) {
                return (new SupplierResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("supplier not found");
            }
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            $supplier->update($data);
            return (new SupplierResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully update supplier");
        } catch (\Throwable $e) {
            return (new SupplierResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function delete($id): Responsable
    {
        try {
            Supplier::destroy($id);
            return (new SupplierResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully delete supplier");
        } catch (\Throwable $e) {
            return (new SupplierResource(null))
                ->withMessage($e->getMessage());
        }
    }
}
