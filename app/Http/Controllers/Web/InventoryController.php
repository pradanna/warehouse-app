<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Http\Resources\Inventory\InventoryResource;
use App\Schemas\Inventory\InventoryQuery;
use App\Schemas\Inventory\InventorySchema;
use App\Services\Inventory\InventoryService;

class InventoryController extends CustomController
{
    /** @var InventoryService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new InventoryService();
    }

    public function create()
    {
        $schema = (new InventorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new InventoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new InventoryQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new InventoryCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new InventoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findBySku($sku)
    {
        $response = $this->service->findBySku($sku);
        return (new InventoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new InventorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new InventoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new InventoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
