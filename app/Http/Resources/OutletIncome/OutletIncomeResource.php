<?php

namespace App\Http\Resources\OutletIncome;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletIncomeResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        $response = [
            'id' => $this->id,
            'date' => $this->date,
            'name' => $this->name,
            'cash' => $this->cash,
            'digital' => $this->digital,
            'total' => $this->total,
            'by_mutation' => $this->by_mutation,
            'description' => $this->description,
        ];
        if ($this->relationLoaded('outlet')) {
            $response['outlet'] = $this->outlet ? [
                'id' => $this->outlet->id,
                'name' => $this->outlet->name
            ] : null;
        }

        if ($this->relationLoaded('author')) {
            $response['author'] = $this->author ? [
                'id' => $this->author->id,
                'username' => $this->author->username
            ] : null;
        }
        return $response;
    }
}
