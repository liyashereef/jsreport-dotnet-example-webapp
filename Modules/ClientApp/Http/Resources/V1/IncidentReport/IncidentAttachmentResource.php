<?php

namespace Modules\ClientApp\Http\Resources\V1\IncidentReport;

use Illuminate\Http\Resources\Json\Resource;

class IncidentAttachmentResource extends Resource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $customerId = $request->customerId;
        return [
            'name' => $this->short_description,
            'url' => route('client.filedownload',[$this->attachment_id,'incident'])."?customerId=".$customerId,
            'ext' => $this->attachment->original_ext,
            'extAssumed' => $this->attachment->assumed_ext
        ];
    }
}
