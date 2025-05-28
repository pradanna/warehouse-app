<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\InventoryAdjustment\InventoryAdjustmentCollection;
use App\Http\Resources\InventoryAdjustment\InventoryAdjustmentResource;
use App\Schemas\InventoryAdjustment\InventoryAdjustmentQuery;
use App\Schemas\InventoryAdjustment\InventoryAdjustmentSchema;
use App\Services\InventoryAdjustment\InventoryAdjustmentService;
use Illuminate\Http\Request;

class InventoryAdjustmentController extends CustomController
{
    //
    /** @var InventoryAdjustmentService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new InventoryAdjustmentService();
    }

    public function create()
    {
        $schema = (new InventoryAdjustmentSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new InventoryAdjustmentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new InventoryAdjustmentQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new InventoryAdjustmentCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new InventoryAdjustmentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
