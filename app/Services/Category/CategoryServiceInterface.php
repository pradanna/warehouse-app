<?php

namespace App\Services\Category;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;

interface CategoryServiceInterface
{
    public function create(CategorySchema $schema): Responsable;
    public function findAll(CategoryQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
    public function patch($id, CategorySchema $schema): Responsable;
    public function delete($id): Responsable;
}
