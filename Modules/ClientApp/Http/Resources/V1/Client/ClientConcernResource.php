<?php

namespace Modules\ClientApp\Http\Resources\V1\Client;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;

class ClientConcernResource extends Resource
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
            'severity' => $this->severityLevel->severity,
            'info' => $this->concern,
            'createdBy'=> new UserResource($this->createdUser),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
