<?php

namespace Modules\KeyManagement\Http\Resources\V1\KeyLogDetail;

use Illuminate\Http\Resources\Json\ResourceCollection;

class KeyLogDetailCollection extends ResourceCollection
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