<?php

namespace App\Services\Category;

use App\Commons\Http\ServiceResponse;
use App\Models\Category;
use App\Schemas\Category\CategorySchema;

class CategoryService implements CategoryServiceInterface
{
    public function create(CategorySchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
            $data = [
                'name' => $schema->getName(),
                'description' => $schema->getDescription()
            ];
            Category::create($data);
            return ServiceResponse::statusCreated("successfully create category");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
