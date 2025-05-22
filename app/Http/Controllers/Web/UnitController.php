<?php

namespace App\Http\Controllers\Web;

use App\Commons\Http\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\Unit\UnitCollection;
use App\Http\Resources\Unit\UnitResource;
use App\Schemas\Unit\UnitQuery;
use App\Schemas\Unit\UnitSchema;
use App\Services\Unit\UnitService;
use Illuminate\Http\Request;

class UnitController extends CustomController
{
    /** @var UnitService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UnitService();
    }

    public function create()
    {
        $schema = (new UnitSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new UnitResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new UnitQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new UnitCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findById($id)
    {
        $response = $this->service->findById($id);
        return (new UnitResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {

        $schema = (new UnitSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new UnitResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new UnitResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
