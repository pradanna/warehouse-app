<?php

namespace App\Http\Resources\InventoryMovement;

use App\Commons\Http\BaseApiResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends BaseApiResource
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
            'type' => $this->type,
            'quantity_open' => $this->quantity_open,
            'quantity' => $this->quantity,
            'quantity_close' => $this->quantity_close,
            'movement_type' => $this->movement_type,
            'movement_reference' => $this->movement_reference,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format("Y-m-d H:i:s"),
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
