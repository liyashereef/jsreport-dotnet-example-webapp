<?php

namespace Modules\KeyManagement\Http\Resources\V1\CustomerKeyDetail;

use Illuminate\Http\Resources\Json\Resource;

class AttachmentResource extends Resource
{
    protected $attachment;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'url' => route('keymanagement.filedownload',[$this->attachment_id,'keymanagement']),
        ];
    }
}
