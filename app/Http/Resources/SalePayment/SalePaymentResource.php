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
        return [
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'payment_type' => $this->payment_type,
            'evidence' => $this->evidence ? url($this->evidence) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sale' => $this->relationLoaded('sale') && $this->sale ? [
                'id' => $this->sale->id,
                'date' => $this->sale->date,
                'reference_number' => $this->sale->reference_number,
                'sub_total' => $this->sale->sub_total,
                'tax' => $this->sale->tax,
                'discount' => $this->sale->discount,
                'total' => $this->sale->total,
                'payment_type' => $this->sale->payment_type
            ] : null,
            'credit' => $this->relationLoaded('sale') && $this->sale ? (
                $this->sale->credit ? [
                    'id' => $this->sale->credit->id,
                    'amount_due' => $this->sale->credit->amount_due,
                    'amount_paid' => $this->sale->credit->amount_paid,
                    'amount_rest' => $this->sale->credit->amount_rest,
                ] : null
            ) : null,
            'outlet' => $this->relationLoaded('sale') && $this->sale ? ($this->sale->outlet ?  [
                'id' => $this->sale->outlet->id,
                'name' => $this->sale->outlet->name
            ] : null) : null,
            'author' => $this->relationLoaded('author') && $this->author ? [
                'id' => $this->author->id,
                'username' => $this->author->username
            ] : null,
        ];
    }
}
