<?php

namespace Modules\KeyManagement\Http\Resources\V1\Attachment;

use Illuminate\Http\Resources\Json\Resource;

class AttachmentResource extends Resource
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
            'name' => $this->short_description,
            'url' => route('keymanagement.filedownload',[$this->attachment_id,'keymanagement']),
            
            
        ];
    }
}
