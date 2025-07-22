<?php

namespace App\Http\Resources\ExpenseCategory;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
