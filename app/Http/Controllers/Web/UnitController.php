<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Schemas\Unit\UnitQuery;
use App\Schemas\Unit\UnitSchema;
use App\Services\Unit\UnitService;
use Illuminate\Http\Request;

class UnitController extends CustomController
{
    /** @var UnitService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UnitService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new UnitSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new UnitQuery();
        $query->hydrateSchemaQuery($queryParams);
        return $this->service->findAll($query);
    }

    public function findById($id)
    {
        return $this->service->findById($id);
    }

    public function patch($id)
    {

        $body = $this->jsonBody();
        $schema = new UnitSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->patch($id, $schema);
    }

    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
