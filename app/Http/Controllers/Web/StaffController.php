<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\Staff\StaffCollection;
use App\Http\Resources\Staff\StaffResource;
use App\Schemas\Staff\StaffQuery;
use App\Schemas\Staff\StaffSchema;
use App\Services\Staff\StaffService;
use Illuminate\Http\Request;

class StaffController extends CustomController
{
    /** @var StaffService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new StaffService();
    }

    public function create()
    {
        $schema = (new StaffSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new StaffResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new StaffQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new StaffCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new StaffResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new StaffSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new StaffResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new StaffResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
