<?php

namespace App\Http\Resources\InventoryAdjustment;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryAdjustmentResource extends BaseApiResource
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
            'item' => null,
            'unit' => null,
            'date' => $this->date,
            'quantity' => $this->quantity,
            'type' => $this->type
        ];

        if ($this->relationLoaded('inventory') && $this->inventory && $this->inventory->relationLoaded('item')) {
            $item = $this->inventory->getRelation('item');
            $response['item'] = $item ? [
                'id' => $item->id,
                'name' => $item->name
            ] : null;
        }

        if ($this->relationLoaded('inventory') && $this->inventory && $this->inventory->relationLoaded('unit')) {
            $unit = $this->inventory->getRelation('unit');
            $response['unit'] = $unit ? [
                'id' => $unit->id,
                'name' => $unit->name
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
