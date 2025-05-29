<?php

namespace App\Services\Sale;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;

interface SaleServiceInterface
{
    public function create(SaleSchema $schema): ServiceResponse;
    public function findAll(SaleQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function summary(SaleQuery $queryParams): ServiceResponse;
}
