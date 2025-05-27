<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\PurchasePayment\PurchasePaymentCollection;
use App\Http\Resources\PurchasePayment\PurchasePaymentResource;
use App\Schemas\PurchasePayment\PurchasePaymentEvidenceSchema;
use App\Schemas\PurchasePayment\PurchasePaymentQuery;
use App\Schemas\PurchasePayment\PurchasePaymentSchema;
use App\Services\PurchasePayment\PurchasePaymentService;
use Illuminate\Http\Request;

class PurchasePaymentController extends CustomController
{
    /** @var PurchasePaymentService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PurchasePaymentService();
    }

    public function create()
    {
        $schema = (new PurchasePaymentSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new PurchasePaymentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new PurchasePaymentQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new PurchasePaymentCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new PurchasePaymentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function uploadEvidence($id)
    {
        $schema = (new PurchasePaymentEvidenceSchema())->hydrateSchemaBody($this->formBody());
        $response = $this->service->uploadEvidence($id, $schema);
        return (new PurchasePaymentResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
