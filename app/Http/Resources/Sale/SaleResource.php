<?php

namespace App\Http\Resources\Sale;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class SaleResource extends BaseApiResource
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
            'reference_number' => $this->reference_number,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
            'description' => $this->description,
            'payment_type' => $this->payment_type,
            'payment_status' => $this->payment_status,
            'outlet' => $this->whenLoaded('outlet', function () {
                return [
                    'id' => $this->outlet->id,
                    'name' => $this->outlet->name,
                ];
            }),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->relationLoaded('inventory') && $item->inventory ? optional($item->inventory->item)->name : null,
                        'unit' => $item->relationLoaded('inventory') && $item->inventory ? optional($item->inventory->unit)->name : null,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->total
                    ];
                });
            }),
            'payments' => $this->whenLoaded('payments', function () {
                return $this->payments->map(function ($payment) {
                    return [
                        'date' => $payment->date,
                        'payment_type' => $payment->payment_type,
                        'amount' => $payment->amount,
                        'description' => $payment->description,
                        'evidence' => $payment->evidence,
                    ];
                });
            }),
            'credit' => $this->relationLoaded('credit') && $this->credit ? [
                'id' => $this->credit->id,
                'amount_due' => $this->credit->amount_due,
                'amount_paid' => $this->credit->amount_paid,
                'amount_rest' => $this->credit->amount_rest,
                'due_date' => $this->credit->due_date,

            ] : null,
            'author' => $this->relationLoaded('author') ? [
                'id' => $this->author->id,
                'username' => $this->author->username
            ] : null
        ];
    }
}
