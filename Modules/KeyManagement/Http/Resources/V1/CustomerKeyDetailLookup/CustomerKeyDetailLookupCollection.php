<?php

namespace Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetailLookup;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerKeyDetailLookupCollection extends ResourceCollection
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