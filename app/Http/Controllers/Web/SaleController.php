<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleResource;
use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;
use App\Services\Sale\SaleService;

class SaleController extends CustomController
{
    /** @var SaleService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new SaleService();
    }

    public function create()
    {
        $schema = (new SaleSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new SaleResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new SaleQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new SaleCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new SaleResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
