<?php

namespace App\Services\Outlet;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Outlet\OutletQuery;
use App\Schemas\Outlet\OutletSchema;

interface OutletServiceInterface
{
    public function create(OutletSchema $schema): ServiceResponse;
    public function findAll(OutletQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, OutletSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
