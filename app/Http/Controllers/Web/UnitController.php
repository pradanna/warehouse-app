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

    private $unitResource;
    private $unitCollection;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UnitService();
        $this->unitResource = null;
        $this->unitCollection = [];
    }

    public function create()
    {
        $schema = (new UnitSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        $this->unitResource = $response->getData();
        return (new UnitResource($this->unitResource))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new UnitQuery();
        $query->hydrateSchemaQuery($queryParams);
        $response = $this->service->findAll($query);
        if ($response->isSuccess()) {
            $this->unitCollection = $response->getData();
        }
        return (new UnitCollection($this->unitCollection))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findById($id)
    {
        $response = $this->service->findById($id);
        if ($response->isSuccess()) {
            $this->unitResource = $response->getData();
        }
        return (new UnitResource($this->unitResource))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {

        $body = $this->jsonBody();
        $schema = new UnitSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->patch($id, $schema);
        if ($response->isSuccess()) {
            $this->unitResource = $response->getData();
        }
        return (new UnitResource($this->unitResource))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        if ($response->isSuccess()) {
            $this->unitResource = $response->getData();
        }
        return (new UnitResource($this->unitResource))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
