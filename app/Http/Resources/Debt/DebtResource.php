<?php

namespace App\Http\Resources\Debt;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends BaseApiResource
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

        if ($this->relationLoaded('purchase') && $this->purchase) {
            $supplier = $this->purchase->getRelation('supplier');
            $response['supplier'] = $supplier ? [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ] : null;
        }

        if ($this->relationLoaded('purchase')) {
            $response['purchase'] = $this->purchase ? [
                'id' => $this->purchase->id,
                'date' => $this->purchase->date,
                'reference_number' => $this->purchase->reference_number,
                'sub_total' => $this->purchase->sub_total,
                'discount' => $this->purchase->discount,
                'tax' => $this->purchase->tax,
                'total' => $this->purchase->total,
            ] : null;
        }


        return $response;
    }
}
