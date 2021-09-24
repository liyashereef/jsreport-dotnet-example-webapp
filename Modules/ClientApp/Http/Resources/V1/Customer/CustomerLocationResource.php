<?php

namespace Modules\ClientApp\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\Resource;

class CustomerLocationResource extends Resource
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
            'lat' => (double)$this->geo_location_lat,
            'lng' => (double)$this->geo_location_long,
        ];
    }
}
