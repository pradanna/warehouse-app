<?php

namespace App\Services\Unit;

use App\Schemas\Unit\UnitSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Models\Unit;
use App\Schemas\Unit\UnitQuery;

class UnitService implements UnitServiceInterface
{
    public function create(UnitSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName()
            ];
            Unit::create($data);
            return ServiceResponse::statusCreated("successfully create unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(UnitQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = Unit::with([])
                ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                });
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData();
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get units", $data, $meta);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findById($id): ServiceResponse
    {
        try {
            $unit = Unit::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$unit) {
                return ServiceResponse::notFound("unit not found");
            }
            return ServiceResponse::statusOK("successfully get unit", $unit);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, UnitSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();

            $unit = Unit::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$unit) {
                return ServiceResponse::notFound("unit not found");
            }

            $data = [
                'name' => $schema->getName()
            ];

            $unit->update($data);
            return ServiceResponse::statusOK("successfully update unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Unit::destroy($id);
            return ServiceResponse::statusOK("successfully delete unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
