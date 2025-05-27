<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\SalePayment\SalePaymentCollection;
use App\Http\Resources\SalePayment\SalePaymentResource;
use App\Schemas\SalePayment\SalePaymentQuery;
use App\Schemas\SalePayment\SalePaymentSchema;
use App\Services\SalePayment\SalePaymentService;
use Illuminate\Http\Request;

class SalePaymentController extends CustomController
{
    /** @var SalePaymentService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new SalePaymentService();
    }

    public function create()
    {
        $schema = (new SalePaymentSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new SalePaymentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new SalePaymentQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new SalePaymentCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new SalePaymentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
