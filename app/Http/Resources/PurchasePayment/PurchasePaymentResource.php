<?php

namespace App\Http\Resources\PurchasePayment;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchasePaymentResource extends BaseApiResource
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
            'purchase' => $this->relationLoaded('purchase') && $this->purchase ? [
                'id' => $this->purchase->id,
                'date' => $this->purchase->date,
                'reference_number' => $this->purchase->reference_number,
                'sub_total' => $this->purchase->sub_total,
                'tax' => $this->purchase->tax,
                'discount' => $this->purchase->discount,
                'total' => $this->purchase->total,
                'payment_type' => $this->purchase->payment_type
            ] : null,
            'supplier' => $this->relationLoaded('purchase') && $this->purchase ? ($this->purchase->supplier ?  [
                'id' => $this->purchase->supplier->id,
                'name' => $this->purchase->supplier->name
            ] : null) : null,
            'author' => $this->relationLoaded('author') && $this->author ? [
                'id' => $this->author->id,
                'username' => $this->author->username
            ] : null,
        ];
    }
}
