<?php

namespace App\Services\Outlet;

use App\Commons\Http\HttpStatus;
use App\Schemas\Outlet\OutletSchema;
use App\Commons\Http\ServiceResponse;
use App\Http\Resources\Outlet\OutletCollection;
use App\Http\Resources\Outlet\OutletResource;
use App\Models\Outlet;
use App\Schemas\Outlet\OutletQuery;

class OutletService implements OutletServiceInterface
{
    public function create(OutletSchema $schema): ServiceResponse
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
            $outlet = Outlet::create($data);
            return ServiceResponse::statusCreated("successfully create outlet", $outlet);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(OutletQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Outlet::with([])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get outlets", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $outlet = Outlet::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$outlet) {
                return ServiceResponse::notFound("outlet not found");
            }
            return ServiceResponse::statusOK("successfully get outlet", $outlet);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, OutletSchema $schema): ServiceResponse
    {

        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $outlet = Outlet::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$outlet) {
                return ServiceResponse::notFound("outlet not found");
            }
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            $outlet->update($data);
            return ServiceResponse::statusOK("successfully update outlet", $outlet);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Outlet::destroy($id);
            return ServiceResponse::statusOK("successfully delete outlet");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
