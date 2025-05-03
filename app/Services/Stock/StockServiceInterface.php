<?php

namespace App\Services\Stock;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Stock\StockQuery;
use App\Schemas\Stock\StockSchema;

interface StockServiceInterface
{
    public function create(StockSchema $schema): ServiceResponse;
    public function findAll(StockQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, StockSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
