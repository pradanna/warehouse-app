<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Outlet\OutletQuery;
use App\Schemas\Outlet\OutletSchema;
use App\Services\Outlet\OutletService;
use Illuminate\Http\Request;

class OutletController extends CustomController
{
    /** @var OutletService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new OutletService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new OutletSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new OutletQuery();
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
        $schema = new OutletSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->patch($id, $schema);
    }

    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
