<?php

namespace App\Services\Unit;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Unit\UnitQuery;
use App\Schemas\Unit\UnitSchema;
use Illuminate\Contracts\Support\Responsable;

interface UnitServiceInterface
{
    public function create(UnitSchema $schema): Responsable;
    public function findAll(UnitQuery $queryParams): Responsable;
    public function findById($id): Responsable;
    public function patch($id, UnitSchema $schema): Responsable;
    public function delete($id): Responsable;
}
