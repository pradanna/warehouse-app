<?php

namespace App\Services\Category;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use Illuminate\Http\Resources\Json\JsonResource;

interface CategoryServiceInterface
{
    public function create(CategorySchema $schema): ServiceResponse;
    public function findAll(CategoryQuery $queryParams): JsonResource;
    public function findByID($id): JsonResource;
    public function patch($id, CategorySchema $schema): ServiceResponse;
    public function delete($id): ServiceResponse;
}
