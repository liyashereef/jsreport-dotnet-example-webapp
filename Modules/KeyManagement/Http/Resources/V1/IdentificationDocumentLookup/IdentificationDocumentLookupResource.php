<?php

namespace Modules\KeyManagement\Http\Resources\V1\IdentificationDocumentLookup;

use Illuminate\Http\Resources\Json\Resource;

class IdentificationDocumentLookupResource extends Resource
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
            'name' => $this->name,
        ];
    }
}
