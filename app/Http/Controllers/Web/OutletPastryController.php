<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\OutletPastry\OutletPastryCollection;
use App\Http\Resources\OutletPastry\OutletPastryResource;
use App\Schemas\OutletPastry\OutletPastryQuery;
use App\Schemas\OutletPastry\OutletPastrySchema;
use App\Services\OutletPastry\OutletPastryService;
use Illuminate\Http\Request;

class OutletPastryController extends CustomController
{
    /** @var OutletPastryService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new OutletPastryService();
    }

    public function create()
    {
        $schema = (new OutletPastrySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new OutletPastryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new OutletPastryQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new OutletPastryCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new OutletPastryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
