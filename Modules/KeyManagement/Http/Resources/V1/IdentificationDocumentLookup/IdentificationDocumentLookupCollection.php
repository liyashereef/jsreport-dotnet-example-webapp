<?php

namespace Modules\KeyManagement\Http\Resources\V1\IdentificationDocumentLookup;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IdentificationDocumentLookupCollection extends ResourceCollection
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