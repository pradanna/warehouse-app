<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Schemas\Stock\StockQuery;
use App\Schemas\Stock\StockSchema;
use App\Services\Stock\StockService;
use Illuminate\Http\Request;

class StockController extends CustomController
{
    /** @var StockService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new StockService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new StockSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->create($schema);
        return $this->toJSON($response);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new StockQuery();
        $query->hydrateSchemaQuery($queryParams);
        $response = $this->service->findAll($query);
        return $this->toJSON($response);
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return $this->toJSON($response);
    }

    public function patch($id)
    {
        $body = $this->jsonBody();
        $schema = new StockSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->patch($id, $schema);
        return $this->toJSON($response);
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return $this->toJSON($response);
    }
}
