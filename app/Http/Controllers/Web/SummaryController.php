<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\Credit\CreditSummaryResource;
use App\Http\Resources\Debt\DebtSummaryResource;
use App\Http\Resources\InventoryMovement\InventoryMovementSummaryCollection;
use App\Http\Resources\Purchase\PurchaseSummaryResource;
use App\Schemas\Credit\CreditQuery;
use App\Schemas\Debt\DebtQuery;
use App\Schemas\InventoryMovement\InventoryMovementQuery;
use App\Schemas\Purchase\PurchaseQuery;
use App\Schemas\Sale\SaleQuery;
use App\Services\Credit\CreditService;
use App\Services\Debt\DebtService;
use App\Services\InventoryMovement\InventoryMovementService;
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

    public function debt()
    {
        $service = new DebtService();
        $query = (new DebtQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $service->summary($query);
        return (new DebtSummaryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function credit()
    {
        $service = new CreditService();
        $query = (new CreditQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $service->summary($query);
        return (new CreditSummaryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function inventoryMovement()
    {
        $service = new InventoryMovementService();
        $query = (new InventoryMovementQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $service->summary($query);
        return (new InventoryMovementSummaryCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
