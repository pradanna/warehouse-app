<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Purchase\PurchaseQuery;
use App\Schemas\Purchase\PurchaseSchema;
use App\Services\Purchase\PurchaseService;
use Illuminate\Http\Request;

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
        $body = $this->jsonBody();
        $schema = new PurchaseSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->create($schema);
        return $this->toJSON($response);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new PurchaseQuery();
        $query->hydrateSchemaQuery($queryParams);
        $response = $this->service->findAll($query);
        return $this->toJSON($response);
    }
}
