<?php

namespace Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetailLookup;

use Illuminate\Http\Resources\Json\Resource;

class CustomerKeyDetailLookupResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'room_name' => $this->room_name,
            'key_id' => $this->key_id,
        ];
    }
}
