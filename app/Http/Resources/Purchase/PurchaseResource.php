<?php

namespace App\Http\Resources\Purchase;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class PurchaseResource extends BaseApiResource
{
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
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
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
            'author' => $this->relationLoaded('author') ? [
                'id' => $this->author->id,
                'username' => $this->author->username
            ] : null
        ];
    }
}
