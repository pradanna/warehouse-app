<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\Purchase\PurchaseSummaryResource;
use App\Schemas\Purchase\PurchaseQuery;
use App\Schemas\Sale\SaleQuery;
use App\Services\Purchase\PurchaseService;
use App\Services\Sale\SaleService;
use Illuminate\Http\Request;

class SummaryController extends CustomController
{
    public function purchase()
    {
        $service = new PurchaseService();
        $query = (new PurchaseQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $service->summary($query);
        return (new PurchaseSummaryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function sale()
    {
        $service = new SaleService();
        $query = (new SaleQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $service->summary($query);
        return (new PurchaseSummaryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
