<?php

namespace App\Services\Unit;

use App\Commons\Http\HttpStatus;
use App\Schemas\Unit\UnitSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Http\Resources\Unit\UnitCollection;
use App\Http\Resources\Unit\UnitResource;
use App\Models\Unit;
use App\Schemas\Unit\UnitQuery;
use Illuminate\Contracts\Support\Responsable;

class UnitService implements UnitServiceInterface
{
    public function create(UnitSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName()
            ];
            $unit = Unit::create($data);
            return ServiceResponse::statusCreated("successfully create unit", $unit);
            // return (new UnitResource(null))
            //     ->withStatus(HttpStatus::Created)
            //     ->withMessage("successfully create unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
            // return (new UnitResource(null))
            //     ->withMessage($e->getMessage());
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
                })
                ->orderBy('name', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get units", $data);
            // return (new UnitCollection($data))
            //     ->withStatus(HttpStatus::OK)
            //     ->withMessage('successfully retrieved units');
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
            // return (new UnitResource(null))
            //     ->withMessage($e->getMessage());
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
                // return (new UnitResource(null))
                //     ->withStatus(HttpStatus::NotFound)
                //     ->withMessage("unit not found");
            }
            return ServiceResponse::statusOK("successfully get unit", $unit);
            // return (new UnitResource($unit))
            //     ->withStatus(HttpStatus::OK)
            //     ->withMessage("successfully retrieved unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
            // return (new UnitResource(null))
            //     ->withMessage($e->getMessage());
        }
    }

    public function patch($id, UnitSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
                // return (new UnitResource(null))
                //     ->additional(['errors' => $validator->errors()->toArray()])
                //     ->withStatus(HttpStatus::UnprocessableEntity)
                //     ->withMessage("error validation");
            }
            $schema->hydrateBody();

            $unit = Unit::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$unit) {
                return ServiceResponse::notFound("unit not found");
                // return (new UnitResource(null))
                //     ->withStatus(HttpStatus::NotFound)
                //     ->withMessage("unit not found");
            }

            $data = [
                'name' => $schema->getName()
            ];

            $unit->update($data);
            return ServiceResponse::statusOK("successfully update unit", $unit);
            // return (new UnitResource(null))
            //     ->withStatus(HttpStatus::OK)
            //     ->withMessage("successfully update unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
            // return (new UnitResource(null))
            //     ->withMessage($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        try {
            Unit::destroy($id);
            return ServiceResponse::statusOK("successfully delete unit");
            // return (new UnitResource(null))
            //     ->withStatus(HttpStatus::OK)
            //     ->withMessage("successfully delete unit");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
            // return (new UnitResource(null))
            //     ->withMessage($e->getMessage());
        }
    }
}
