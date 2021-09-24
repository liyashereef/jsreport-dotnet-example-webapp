<?php

namespace Modules\KeyManagement\Http\Resources\V1\User;
use Illuminate\Http\Resources\Json\Resource;



class UserResource extends Resource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
        ];
    }

}
