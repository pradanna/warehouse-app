<?php

namespace App\Http\Resources\Item;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class ItemResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name
                ];
            }),
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
