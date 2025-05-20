<?php

namespace App\Services\Purchase;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Purchase\PurchaseQuery;
use App\Schemas\Purchase\PurchaseSchema;
use Illuminate\Contracts\Support\Responsable;

interface PurchaseServiceInterface
{
    public function create(PurchaseSchema $schema): Responsable;
    public function findAll(PurchaseQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
}
