<?php

namespace App\Http\Resources\Credit;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends BaseApiResource
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
            'amount_due' => $this->amount_due,
            'amount_paid' => $this->amount_paid,
            'amount_rest' => $this->amount_rest,
            'due_date' => $this->due_date,
            'status' => $this->amount_rest <= 0 ? 'paid' : 'unpaid'
        ];

        if ($this->relationLoaded('sale') && $this->sale) {
            $outlet = $this->sale->getRelation('outlet');
            $response['outlet'] = $outlet ? [
                'id' => $outlet->id,
                'name' => $outlet->name,
            ] : null;
        }

        if ($this->relationLoaded('sale')) {
            $response['sale'] = $this->sale ? [
                'id' => $this->sale->id,
                'date' => $this->sale->date,
                'reference_number' => $this->sale->reference_number,
                'sub_total' => $this->sale->sub_total,
                'discount' => $this->sale->discount,
                'tax' => $this->sale->tax,
                'total' => $this->sale->total,
            ] : null;
        }


        return $response;
    }
}
