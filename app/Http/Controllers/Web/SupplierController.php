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
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new SupplierQuery();
        $query->hydrateSchemaQuery($queryParams);
        return $this->service->findAll($query);
    }

    public function findByID($id)
    {
        return $this->service->findByID($id);
    }

    public function patch($id)
    {
        $body = $this->jsonBody();
        $schema = new SupplierSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->patch($id, $schema);
    }

    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
