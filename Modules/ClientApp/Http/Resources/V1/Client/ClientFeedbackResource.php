<?php

namespace Modules\ClientApp\Http\Resources\V1\Client;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ClientApp\Http\Resources\V1\Rating\RatingResource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;
use Modules\ClientApp\Http\Resources\V1\Customer\CustomerResource;

class ClientFeedbackResource extends Resource
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
            'type' => new ClientFeedbackLookupResource($this->clientFeedbacks),
            'info' => $this->client_feedback,
            'employee'=> new UserResource($this->user),
            'createdBy'=> new UserResource($this->createdUser),
            'rating'=> new RatingResource($this->userRating),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
