<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Http\Resources\Credit\CreditCollection;
use App\Http\Resources\Credit\CreditResource;
use App\Schemas\Credit\CreditQuery;
use App\Services\Credit\CreditService;

class CreditController extends CustomController
{
    /** @var CreditService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CreditService();
    }

    public function findAll()
    {
        $query = (new CreditQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new CreditCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new CreditResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
