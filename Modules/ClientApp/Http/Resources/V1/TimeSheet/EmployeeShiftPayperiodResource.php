<?php

namespace Modules\ClientApp\Http\Resources\V1\TimeSheet;

use Illuminate\Http\Resources\Json\Resource;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;

class EmployeeShiftPayperiodResource extends Resource
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
            'employee' => new UserResource($this->trashed_user),
            'totalHours' => (!$this->total_hours_by_employee->isEmpty())? $this->total_hours_by_employee[0]->total_work_hours: '00:00',
            'transactions' =>  EmployeeShiftResource::collection($this->submitted_shifts)
        ];
    }
}
