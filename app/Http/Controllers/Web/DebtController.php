<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\Debt\DebtCollection;
use App\Http\Resources\Debt\DebtResource;
use App\Schemas\Debt\DebtQuery;
use App\Services\Debt\DebtService;
use Illuminate\Http\Request;

class DebtController extends CustomController
{
    /** @var DebtService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new DebtService();
    }

    public function findAll()
    {
        $query = (new DebtQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new DebtCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new DebtResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
