<?php

namespace Modules\ClientApp\Http\Resources\V1\IncidentReport;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncidentReportCollection extends ResourceCollection
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
