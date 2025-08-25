<?php

namespace App\Http\Resources\OutletPastry;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletPastryResource extends BaseApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'date' => $this->date,
            'reference_number' => $this->reference_number,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'total' => $this->total,
        ];

        if ($this->relationLoaded('outlet')) {
            $data['outlet'] = $this->outlet ? [
                'id' => $this->outlet->id,
                'name' => $this->outlet->name,
            ] : null;
        }

        if ($this->relationLoaded('items')) {
            $data['items'] = $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total
                ];
            });
        }

        if ($this->relationLoaded('author')) {
            $data['author'] = $this->author ? [
                'id' => $this->author->id,
                'username' => $this->author->username
            ] : null;
        }
        return $data;
    }
}
