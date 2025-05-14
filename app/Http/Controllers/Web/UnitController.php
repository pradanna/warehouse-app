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
        $response = $this->service->create($schema);
        return $this->toJSON($response);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new UnitQuery();
        $query->hydrateSchemaQuery($queryParams);
        $response = $this->service->findAll($query);
        return $this->toJSON($response);
    }

    public function findById($id)
    {
        $response = $this->service->findById($id);
        return $this->toJSON($response);
    }

    public function patch($id)
    {

        $body = $this->jsonBody();
        $schema = new UnitSchema();
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
