<?php

namespace App\Http\Resources\FundTransfer;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundTransferResource extends BaseApiResource
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
            'amount' => $this->amount,
            'transfer_to' => $this->transfer_to,
        ];

        if ($this->relationLoaded('outlet')) {
            $response['outlet'] = $this->outlet ? [
                'id' => $this->outlet->id,
                'name' => $this->outlet->name
            ] : null;
        }
        return $response;
    }
}
