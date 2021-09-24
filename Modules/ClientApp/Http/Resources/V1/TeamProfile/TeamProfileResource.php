<?php

namespace Modules\ClientApp\Http\Resources\V1\TeamProfile;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ClientApp\Http\Resources\V1\PayPeriod\PayPeriodResource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;

class TeamProfileResource extends Resource
{

    protected $attachmentRepository;

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
        ];
    }
}
