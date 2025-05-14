<?php

namespace App\Services\Outlet;

use App\Schemas\Outlet\OutletSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Models\Outlet;
use App\Schemas\Outlet\OutletQuery;

class OutletService implements OutletServiceInterface
{
    public function create(OutletSchema $schema): ServiceResponse
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
            Outlet::create($data);
            return ServiceResponse::statusCreated("successfully create outlet");
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
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData()->makeHidden(['created_at', 'updated_at']);
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get outlets", $data, $meta);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $item = Outlet::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("outlet not found");
            }
            $item->makeHidden(['created_at', 'updated_at']);
            return ServiceResponse::statusOK("successfully get outlet", $item);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, OutletSchema $schema): ServiceResponse
    {

        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $item = Outlet::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("outlet not found");
            }
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            $item->update($data);
            return ServiceResponse::statusOK("successfully update outlet");
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
