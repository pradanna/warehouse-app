<?php

namespace App\Services\Outlet;

use App\Schemas\Outlet\OutletQuery;
use App\Schemas\Outlet\OutletSchema;
use Illuminate\Contracts\Support\Responsable;

interface OutletServiceInterface
{
    public function create(OutletSchema $schema): Responsable;
    public function findAll(OutletQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
    public function patch($id, OutletSchema $schema): Responsable;
    public function delete($id): Responsable;
}
