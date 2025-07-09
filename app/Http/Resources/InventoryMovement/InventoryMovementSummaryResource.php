<?php

namespace App\Http\Resources\InventoryMovement;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementSummaryResource extends BaseApiResource
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
            'name' => $this->name,
            'movements' => $this->movements->map(function ($item) {
                return [
                    'date' =>  $item->date,
                    'open' => (float) $item->open,
                    'in' => (float) $item->in,
                    'out' => (float) $item->out,
                    'close' => (float) $item->open + ((float) $item->in - (float) $item->out),

                ];
            })
        ];
    }
}
