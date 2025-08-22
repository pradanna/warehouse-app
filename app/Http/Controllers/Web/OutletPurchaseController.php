<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\OutletPurchase\OutletPurchaseCollection;
use App\Http\Resources\OutletPurchase\OutletPurchaseResource;
use App\Schemas\OutletPurchase\OutletPurchaseQuery;
use App\Schemas\OutletPurchase\OutletPurchaseSchema;
use App\Services\OutletPurchase\OutletPurchaseService;
use Illuminate\Http\Request;

class OutletPurchaseController extends CustomController
{
    /** @var OutletPurchaseService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new OutletPurchaseService();
    }

    public function findAll()
    {
        $query = (new OutletPurchaseQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new OutletPurchaseCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new OutletPurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function create()
    {
        $schema = (new OutletPurchaseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new OutletPurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function update($id)
    {
        $schema = (new OutletPurchaseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->update($id, $schema);
        return (new OutletPurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new OutletPurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
