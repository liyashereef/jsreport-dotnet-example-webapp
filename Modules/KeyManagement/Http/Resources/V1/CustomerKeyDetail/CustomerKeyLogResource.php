<?php

namespace Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetail;

use Illuminate\Http\Resources\Json\Resource;

class CustomerKeyLogResource extends Resource
{
    protected $log;

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
            'checked_out_by' => $this->checked_out_by,
            'checked_out_to' => $this->checked_out_to,
            'company_name' => $this->company_name,
            'notes' => $this->notes,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),

        ];
    }
}
