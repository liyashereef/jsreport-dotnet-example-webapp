<?php

namespace Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetail;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerKeyDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}