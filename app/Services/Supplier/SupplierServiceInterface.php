<?php

namespace App\Services\Supplier;

use App\Schemas\Supplier\SupplierQuery;
use App\Schemas\Supplier\SupplierSchema;
use Illuminate\Contracts\Support\Responsable;

interface SupplierServiceInterface
{
    public function create(SupplierSchema $schema): Responsable;
    public function findAll(SupplierQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
    public function patch($id, SupplierSchema $schema): Responsable;
    public function delete($id): Responsable;
}
