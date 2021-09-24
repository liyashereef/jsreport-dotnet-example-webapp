<?php

namespace Modules\ClientApp\Http\Resources\V1\Rating;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;

class RatingResource extends Resource
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
            'name' => $this->rating,
            'score' => $this->score,
        ];
    }
}
