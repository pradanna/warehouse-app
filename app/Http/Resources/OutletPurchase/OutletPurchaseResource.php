<?php

namespace App\Http\Resources\OutletPurchase;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletPurchaseResource extends BaseApiResource
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
            'cash' => $this->cash,
            'digital' => $this->digital,
            'amount' => $this->amount,
        ];

        if ($this->relationLoaded('outlet')) {
            $outlet = $this->getRelation('outlet');
            $response['outlet'] = $outlet ? [
                'id' => $outlet->id,
                'name' => $outlet->name
            ] : null;
        }

        if ($this->relationLoaded('cash_flow')) {
            $cashFlow = $this->getRelation('cash_flow');
            $response['cash_flow'] = $cashFlow ? [
                'id' => $cashFlow->id,
                'date' => $cashFlow->date,
                'type' => $cashFlow->type,
                'name' => $cashFlow->name,
                'amount' => $cashFlow->amount
            ] : null;
        }

        if ($this->relationLoaded('sale')) {
            $sale = $this->getRelation('sale');
            $response['sale'] = $sale ? [
                'id' => $sale->id,
                'date' => $sale->date,
                'reference_number' => $sale->reference_number,
                'sub_total' => $sale->sub_total,
                'discount' => $sale->discount,
                'tax' => $sale->tax,
                'total' => $sale->total,
                'payment_type' => $sale->payment_type,
                'payment_status' => $sale->payment_status,
            ] : null;
        }
        return $response;
    }
}
