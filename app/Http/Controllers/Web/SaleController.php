<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;
use App\Services\Sale\SaleService;

class SaleController extends CustomController
{
    /** @var SaleService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new SaleService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new SaleSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new SaleQuery();
        $query->hydrateSchemaQuery($queryParams);
        return $this->service->findAll($query);
    }

    public function findByID($id)
    {
        return $this->service->findByID($id);
    }
}
