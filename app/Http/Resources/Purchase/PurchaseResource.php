<?php

namespace App\Http\Resources\Purchase;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class PurchaseResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        $response = [
            'id' => $this->id,
            'date' => $this->date,
            'reference_number' => $this->reference_number,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
            'description' => $this->description,
            'payment_type' => $this->payment_type,
            'payment_status' => $this->payment_status
        ];

        if ($this->relationLoaded('supplier')) {
            $response['supplier'] = $this->supplier ? [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name,
            ] : null;
        }

        if ($this->relationLoaded('items')) {
            $response['items'] = $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->relationLoaded('inventory') && $item->inventory ? optional($item->inventory->item)->name : null,
                    'unit' => $item->relationLoaded('inventory') && $item->inventory ? optional($item->inventory->unit)->name : null,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total
                ];
            });
        }

        if ($this->relationLoaded('payments')) {
            $response['payments'] = $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'date' => $payment->date,
                    'payment_type' => $payment->payment_type,
                    'amount' => $payment->amount,
                    'description' => $payment->description,
                    'evidence' => $payment->evidence ? url($payment->evidence) : null,
                ];
            });
        }

        if ($this->relationLoaded('debt')) {
            $response['debt'] = $this->debt ? [
                'amount_due' => $this->debt->amount_due,
                'amount_paid' => $this->debt->amount_paid,
                'amount_rest' => $this->debt->amount_rest,
                'due_date' => $this->debt->due_date,
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
