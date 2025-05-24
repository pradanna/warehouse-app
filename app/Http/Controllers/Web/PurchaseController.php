<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Http\Resources\Purchase\PurchaseCollection;
use App\Http\Resources\Purchase\PurchaseResource;
use App\Schemas\Purchase\PurchasePaymentSchema;
use App\Schemas\Purchase\PurchaseQuery;
use App\Schemas\Purchase\PurchaseSchema;
use App\Services\Purchase\PurchaseService;

class PurchaseController extends CustomController
{
    /** @var PurchaseService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PurchaseService();
    }

    public function create()
    {
        $schema = (new PurchaseSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new PurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new PurchaseQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new PurchaseCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new PurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function payment($id)
    {
        $schema = (new PurchasePaymentSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->payment($id, $schema);
        return (new PurchaseResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

}
