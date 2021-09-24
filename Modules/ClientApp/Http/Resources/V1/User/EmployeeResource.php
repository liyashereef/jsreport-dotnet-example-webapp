<?php

namespace Modules\ClientApp\Http\Resources\V1\User;

use Illuminate\Http\Resources\Json\Resource;

class EmployeeResource extends Resource
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
            'employee_no' => $this->employee_no,
            'image' => $this->image,
            'address' => $this->employee_full_address,
        ];
    }
}
