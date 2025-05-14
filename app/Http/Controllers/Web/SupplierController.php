<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Supplier\SupplierQuery;
use App\Schemas\Supplier\SupplierSchema;
use App\Services\Supplier\SupplierService;

class SupplierController extends CustomController
{
    /** @var SupplierService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new SupplierService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new SupplierSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->create($schema);
        return $this->toJSON($response);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new SupplierQuery();
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
        $schema = new SupplierSchema();
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
