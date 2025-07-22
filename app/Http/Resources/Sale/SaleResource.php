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

        if ($this->relationLoaded('outlet')) {
            $response['outlet'] = $this->outlet ? [
                'id' => $this->outlet->id,
                'name' => $this->outlet->name,
                'address' => $this->outlet->address,
                'phone' => $this->outlet->phone
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

        if ($this->relationLoaded('credit')) {
            $response['credit'] = $this->credit ? [
                'amount_due' => $this->credit->amount_due,
                'amount_paid' => $this->credit->amount_paid,
                'amount_rest' => $this->credit->amount_rest,
                'due_date' => $this->credit->due_date,
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
