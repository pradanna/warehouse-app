<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\OutletIncome\OutletIncomeCollection;
use App\Http\Resources\OutletIncome\OutletIncomeResource;
use App\Schemas\OutletIncome\OutletIncomeQuery;
use App\Schemas\OutletIncome\OutletIncomeSchema;
use App\Services\OutletIncome\OutletIncomeService;
use Illuminate\Http\Request;

class OutletIncomeController extends CustomController
{
    /** @var OutletIncomeService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new OutletIncomeService();
    }

    public function create()
    {
        $schema = (new OutletIncomeSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new OutletIncomeResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new OutletIncomeQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new OutletIncomeCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
