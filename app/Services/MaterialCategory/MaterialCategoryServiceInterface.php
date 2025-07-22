<?php

namespace App\Services\MaterialCategory;

use App\Commons\Http\ServiceResponse;
use App\Schemas\MaterialCategory\MaterialCategoryQuery;
use App\Schemas\MaterialCategory\MaterialCategorySchema;

interface MaterialCategoryServiceInterface
{
    public function create(MaterialCategorySchema $schema): ServiceResponse;
    public function findAll(MaterialCategoryQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, MaterialCategorySchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
