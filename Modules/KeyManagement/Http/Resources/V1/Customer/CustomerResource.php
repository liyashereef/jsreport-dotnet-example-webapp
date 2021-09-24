<?php

namespace Modules\KeyManagement\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\Resource;

class CustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     *
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'project_number' => $this->project_number,
            'client_name' => $this->client_name,
            'name' => $this->contact_person_name,
            
            
        ];
    }
}
