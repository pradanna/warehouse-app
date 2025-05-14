<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Inventory\InventoryQuery;
use App\Schemas\Inventory\InventorySchema;
use App\Services\Inventory\InventoryService;

class InventoryController extends CustomController
{
    /** @var InventoryService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new InventoryService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new InventorySchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new InventoryQuery();
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
        $schema = new InventorySchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->patch($id, $schema);
    }

    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
