<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\OutletExpense\OutletExpenseCollection;
use App\Http\Resources\OutletExpense\OutletExpenseResource;
use App\Schemas\OutletExpense\OutletExpenseQuery;
use App\Schemas\OutletExpense\OutletExpenseSchema;
use App\Services\OutletExpense\OutletExpenseService;
use Illuminate\Http\Request;

class OutletExpenseController extends CustomController
{
    /** @var OutletExpenseService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new OutletExpenseService();
    }

    public function create()
    {
        $schema = (new OutletExpenseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new OutletExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new OutletExpenseQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new OutletExpenseCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new OutletExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new OutletExpenseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new OutletExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new OutletExpenseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
