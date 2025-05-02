<?php

namespace App\Services\Category;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;

interface CategoryServiceInterface
{
    public function create(CategorySchema $schema): ServiceResponse;
    public function findAll(CategoryQuery $queryParams): ServiceResponse;
    public function findByID($id): ServiceResponse;
    public function patch($id, CategorySchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
