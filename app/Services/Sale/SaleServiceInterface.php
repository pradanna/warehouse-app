<?php

namespace App\Services\Sale;

use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;
use Illuminate\Contracts\Support\Responsable;

interface SaleServiceInterface
{
    public function create(SaleSchema $schema): Responsable;
    public function findAll(SaleQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
}
