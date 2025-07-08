<?php

namespace App\Http\Resources\Staff;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        $data =  [
            'id' => $this->id,
            'username' => $this->username,
        ];

        if ($this->relationLoaded('staff')) {
            $data['profile'] = $this->staff ? [
                'id' => $this->staff->id,
                'name' => $this->staff->name
            ] : null;
        }

        if ($this->relationLoaded('staff') && $this->staff && $this->staff->relationLoaded('outlet')) {
            $outlet = $this->staff->getRelation('outlet');
            $data['outlet'] = $outlet ? [
                'id' => $outlet->id,
                'name' => $outlet->name
            ] : null;
        }
        return $data;
    }
}
