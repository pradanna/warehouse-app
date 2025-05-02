<?php

namespace App\Services\Category;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Category\CategorySchema;

interface CategoryServiceInterface
{
    public function create(CategorySchema $schema): ServiceResponse;
}
