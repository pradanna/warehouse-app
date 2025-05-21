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
    public function create(UnitSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new UnitResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName()
            ];
            Unit::create($data);
            return (new UnitResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create unit");
        } catch (\Throwable $e) {
            return (new UnitResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(UnitQuery $queryParams): Responsable
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
            return (new UnitCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved units');
        } catch (\Throwable $e) {
            return (new UnitResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findById($id): Responsable
    {
        try {
            $unit = Unit::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$unit) {
                return (new UnitResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("unit not found");
            }
            return (new UnitResource($unit))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved unit");
        } catch (\Throwable $e) {
            return (new UnitResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function patch($id, UnitSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new UnitResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();

            $unit = Unit::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$unit) {
                return (new UnitResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("unit not found");
            }

            $data = [
                'name' => $schema->getName()
            ];

            $unit->update($data);
            return (new UnitResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully update unit");
        } catch (\Throwable $e) {
            return (new UnitResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function delete($id): Responsable
    {
        try {
            Unit::destroy($id);
            return (new UnitResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully delete unit");
        } catch (\Throwable $e) {
            return (new UnitResource(null))
                ->withMessage($e->getMessage());
        }
    }
}
