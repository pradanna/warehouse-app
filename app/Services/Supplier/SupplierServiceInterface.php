<?php

namespace App\Services\Supplier;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Supplier\SupplierQuery;
use App\Schemas\Supplier\SupplierSchema;

interface SupplierServiceInterface
{
    public function create(SupplierSchema $schema): ServiceResponse;
    public function findAll(SupplierQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, SupplierSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
