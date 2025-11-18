<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\FundTransfer\FundTransferCollection;
use App\Http\Resources\FundTransfer\FundTransferResource;
use App\Schemas\FundTransfer\FundTransferQuery;
use App\Schemas\FundTransfer\FundTransferSchema;
use App\Services\FundTransfer\FundTransferService;
use Illuminate\Http\Request;

class FundTransferController extends CustomController
{
    /** @var FundTransferService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new FundTransferService();
    }

    public function create()
    {
        $schema = (new FundTransferSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new FundTransferResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new FundTransferQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new FundTransferCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new FundTransferResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new FundTransferSchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new FundTransferResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new FundTransferResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
