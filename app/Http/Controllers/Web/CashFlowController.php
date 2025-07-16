<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\CashFlow\CashFlowCollection;
use App\Schemas\CashFlow\CashFlowQuery;
use App\Services\CashFlow\CashFlowService;
use Illuminate\Http\Request;

class CashFlowController extends CustomController
{
    /** @var CashFlowService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new CashFlowService();
    }

    public function findAll()
    {
        $query = (new CashFlowQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new CashFlowCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
