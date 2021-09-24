<?php

namespace Modules\ClientApp\Http\Resources\V1\Client;

use App\Services\HelperService;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Modules\Admin\Models\User;

class ClientFeedbackLookupResource extends Resource
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
            'name' => $this->feedback,
            'shortName' => $this->short_name,
        ];
    }
}
