<?php

namespace App\Http\Resources\WarehouseExpense;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseExpenseResource extends BaseApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'description' => $this->description,
        ];

        if ($this->relationLoaded('expense_category')) {
            $response['category'] = $this->expense_category ? [
                'id' => $this->expense_category->id,
                'name' => $this->expense_category->name
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
