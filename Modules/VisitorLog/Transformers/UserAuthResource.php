<?php

namespace Modules\VisitorLog\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $permissions = $this->getAllPermissions()->pluck('name');
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'image' => (!empty($this->employee_profile->image))
                ? (url('/') . \Config::get('globals.profilePicPath')
                    . $this->employee_profile->image
                    . "?ts="
                    . strtotime("now"))
                : null,
            'employeeNo' => $this->employee_profile->employee_no,
            'phone' => $this->employee_profile->phone,
            'role' => isset($this->roles[0]) ? $this->roles[0]->name : null,
            'permissions' => $permissions
        ];
    }
}
