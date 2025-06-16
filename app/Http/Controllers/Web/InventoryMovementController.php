<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\InventoryMovement\InventoryMovementCollection;
use App\Http\Resources\InventoryMovement\InventoryMovementResource;
use App\Schemas\InventoryMovement\InventoryMovementQuery;
use App\Services\InventoryMovement\InventoryMovementService;
use Illuminate\Http\Request;

class InventoryMovementController extends CustomController
{
    /** @var InventoryMovementService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new InventoryMovementService();
    }

    public function findAll()
    {
        $query = (new InventoryMovementQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new InventoryMovementCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new InventoryMovementResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
