<?php

namespace App\Http\Resources\Category;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class CategoryResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
