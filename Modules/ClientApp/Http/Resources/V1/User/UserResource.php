<?php

namespace Modules\ClientApp\Http\Resources\V1\User;

use App\Services\HelperService;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Modules\Admin\Models\User;

class UserResource extends Resource
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
            'employeeNo' => $this->trashedEmployee->employee_no,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'emailAlt' => $this->alternate_email,
            'phone' => $this->trashedEmployee->cell_no,
            'phoneAlt' => $this->trashedEmployee->project_number,
            'image' => empty($this->trashedEmployee->image) ? null : asset('images/uploads/').'/'.$this->trashedEmployee->image,
            'role' => isset($this->roles[0]) ? $this->getRoleName($this->roles[0]) : "",
            'permissions' => [],//data_get($this->roles[0]->permissions,"*.name"),
            'online' => $this->liveShiftStatus($this->liveStatus),
            'address' => $this->getEmployeeAddress($this->trashedEmployee),
            'createdAt' => $this->created_at->toDateTimeString(),
        ];
    }

    public function getEmployeeAddress($employee) {
        return  Collection::make(
            [
                'address' => $employee->employee_address,
                'city' => $employee->employee_city,
                'zipCode' => $employee->employee_postal_code
            ]
        );
    }

    public function liveShiftStatus($liveStatus) {
        $online = false;
        if(
            isset($liveStatus->mostRecentShift)
            &&
            (
                $liveStatus->mostRecentShift->live_status_id == AVAILABLE
                ||
                $liveStatus->mostRecentShift->live_status_id == MEETING
            )
        ) {
            $online = true;
        }
        return $online;
    }

    public function getRoleName($roleNameObj) {
        $roleName = $roleNameObj->name;
        $arrCapital = ["ceo","cfo","coo"];
        if(ctype_lower(substr($roleName,0,1))){
            if(in_array($roleName, $arrCapital)) {
                $roleName = strtoupper($roleName);
            } else {
                $roleName = HelperService::snakeToTitleCase($roleName);
            }
        }
        return $roleName;
    }
}
