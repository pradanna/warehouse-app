<?php

namespace App\Services\Unit;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Unit\UnitQuery;
use App\Schemas\Unit\UnitSchema;

interface UnitServiceInterface
{
    public function create(UnitSchema $schema): ServiceResponse;
    public function findAll(UnitQuery $queryParams): ServiceResponse;
    public function findById($id): ServiceResponse;
    public function patch($id, UnitSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
