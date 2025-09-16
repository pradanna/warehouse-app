<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\WarehouseExpense\WarehouseExpenseCollection;
use App\Http\Resources\WarehouseExpense\WarehouseExpenseResource;
use App\Schemas\WarehouseExpense\WarehouseExpenseQuery;
use App\Schemas\WarehouseExpense\WarehouseExpenseSchema;
use App\Services\WarehouseExpense\WarehouseExpenseService;
use Illuminate\Http\Request;

class WarehouseExpenseControler extends CustomController
{
    /** @var WarehouseExpenseService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new WarehouseExpenseService();
    }

    public function create()
    {
        $schema = (new WarehouseExpenseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new WarehouseExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new WarehouseExpenseQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new WarehouseExpenseCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new WarehouseExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new WarehouseExpenseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new WarehouseExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new WarehouseExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
