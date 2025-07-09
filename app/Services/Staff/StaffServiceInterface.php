<?php

namespace App\Services\Staff;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Staff\StaffQuery;
use App\Schemas\Staff\StaffSchema;

interface StaffServiceInterface
{
    public function create(StaffSchema $schema): ServiceResponse;
    public function findAll(StaffQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, StaffSchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
