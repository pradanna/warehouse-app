<?php

namespace App\Http\Resources\CashFlow;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashFlowResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        $response = [
            'date' => $this['date'],
            'data' => $this['data'],
            // 'type' => $this->type,
            // 'name' => $this->name,
            // 'amount' => $this->amount,
            // 'description' => $this->description,
        ];
        // if ($this->relationLoaded('outlet')) {
        //     $response['outlet'] = $this->outlet ? [
        //         'id' => $this->outlet->id,
        //         'name' => $this->outlet->name
        //     ] : null;
        // }

        // if ($this->relationLoaded('author')) {
        //     $response['author'] = $this->author ? [
        //         'id' => $this->author->id,
        //         'username' => $this->author->username
        //     ] : null;
        // }
        return $response;
    }
}
