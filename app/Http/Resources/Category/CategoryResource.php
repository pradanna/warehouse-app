<?php

namespace App\Http\Resources\Category;

use App\Commons\Http\BaseResource;
use Illuminate\Http\Request;

class CategoryResource extends BaseResource
{
    protected function toItemArray($item, Request $request): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'items' => $item->whenLoaded('items')
        ];
    }
}
