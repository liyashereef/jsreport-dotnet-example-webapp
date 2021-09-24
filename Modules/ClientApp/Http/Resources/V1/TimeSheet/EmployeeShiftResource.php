<?php

namespace Modules\ClientApp\Http\Resources\V1\TimeSheet;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class EmployeeShiftResource extends Resource
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
            'start' => $this->start,
            'end' => $this->end,
            'workHours' => implode(':', explode(':', $this->work_hours, -1))
        ];
    }
}
