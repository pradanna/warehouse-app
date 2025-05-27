<?php

namespace App\Http\Resources\SalePayment;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalePaymentResource extends BaseApiResource
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
            'payment_type' => $this->payment_type,
            'evidence' => $this->evidence ? url($this->evidence) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->relationLoaded('sale')) {
            $response['sale'] = $this->sale ? [
                'id' => $this->sale->id,
                'date' => $this->sale->date,
                'reference_number' => $this->sale->reference_number,
                'sub_total' => $this->sale->sub_total,
                'tax' => $this->sale->tax,
                'discount' => $this->sale->discount,
                'total' => $this->sale->total,
                'payment_type' => $this->sale->payment_type
            ] : null;
        }

        if (
            $this->relationLoaded('sale') &&
            $this->sale &&
            $this->sale->relationLoaded('credit')
        ) {
            $credit = $this->sale->getRelation('credit');
            $response['credit'] = $credit ? [
                'id' => $credit->id,
                'amount_due' => $credit->amount_due,
                'amount_paid' => $credit->amount_paid,
                'amount_rest' => $credit->amount_rest,
            ] : null;
        }

        if (
            $this->relationLoaded('sale') &&
            $this->sale &&
            $this->sale->relationLoaded('outlet')
        ) {
            $outlet = $this->sale->getRelation('outlet');
            $response['outlet'] = $outlet ? [
                'id' => $outlet->id,
                'name' => $outlet->name
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
