<?php

namespace App\Services\Outlet;

use App\Commons\Http\HttpStatus;
use App\Schemas\Outlet\OutletSchema;
use App\Commons\Http\ServiceResponse;
use App\Http\Resources\Outlet\OutletCollection;
use App\Http\Resources\Outlet\OutletResource;
use App\Models\Outlet;
use App\Schemas\Outlet\OutletQuery;
use Illuminate\Contracts\Support\Responsable;

class OutletService implements OutletServiceInterface
{
    public function create(OutletSchema $schema): Responsable
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new OutletResource(null))
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
            Outlet::create($data);
            return (new OutletResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create outlet");
        } catch (\Throwable $e) {
            return (new OutletResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(OutletQuery $queryParams): Responsable
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
            return (new OutletCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved outlets');
        } catch (\Throwable $e) {
            return (new OutletResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
    {
        try {
            $outlet = Outlet::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$outlet) {
                return (new OutletResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("outlet not found");
            }
            return (new OutletResource($outlet))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved outlet");
        } catch (\Throwable $e) {
            return (new OutletResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function patch($id, OutletSchema $schema): Responsable
    {

        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new OutletResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $outlet = Outlet::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$outlet) {
                return (new OutletResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("outlet not found");
            }
            $data = [
                'name' => $schema->getName(),
                'address' => $schema->getAddress(),
                'contact' => $schema->getContact()
            ];
            $outlet->update($data);
            return (new OutletResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully update outlet");
        } catch (\Throwable $e) {
            return (new OutletResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function delete($id): Responsable
    {
        try {
            Outlet::destroy($id);
            return (new OutletResource(null))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully delete outlet");
        } catch (\Throwable $e) {
            return (new OutletResource(null))
                ->withMessage($e->getMessage());
        }
    }
}
