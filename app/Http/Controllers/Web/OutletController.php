<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Http\Resources\Outlet\OutletCollection;
use App\Http\Resources\Outlet\OutletResource;
use App\Schemas\Outlet\OutletQuery;
use App\Schemas\Outlet\OutletSchema;
use App\Services\Outlet\OutletService;
use Illuminate\Http\Request;

class OutletController extends CustomController
{
    /** @var OutletService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new OutletService();
    }

    public function create()
    {
        $schema = (new OutletSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new OutletResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new OutletQuery())->hydrateSchemaBody($this->queryParams());
        $response = $this->service->findAll($query);
        return (new OutletCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new OutletResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new OutletSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new OutletResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new OutletResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
