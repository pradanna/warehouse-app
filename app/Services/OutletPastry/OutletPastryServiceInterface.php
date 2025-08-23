<?php

namespace App\Services\OutletPastry;

use App\Commons\Http\ServiceResponse;
use App\Schemas\OutletPastry\OutletPastryQuery;
use App\Schemas\OutletPastry\OutletPastrySchema;

interface OutletPastryServiceInterface
{
    public function findAll(OutletPastryQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function create(OutletPastrySchema $schema): ServiceResponse;
}
