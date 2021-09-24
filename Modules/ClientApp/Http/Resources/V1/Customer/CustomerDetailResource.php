<?php

namespace Modules\ClientApp\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use Modules\Admin\Models\User;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;

class CustomerDetailResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $supervisorId = (isset($this->employeeLatestCustomerSupervisor) && !empty($this->employeeLatestCustomerSupervisor->id)) ? $this->employeeLatestCustomerSupervisor->id : null;
        $areaManagerId = (isset($this->employeeCustomerAreaManager) && !empty($this->employeeCustomerAreaManager->id)) ? $this->employeeCustomerAreaManager->id : null;
        return [
            'id' => $this->id,
            'name' => $this->client_name,
            'projectNo' => $this->project_number,
            'address' => $this->getCustomerAddress($this),
            'contactName' => $this->contact_person_name,
            'contactEmail' => $this->contact_person_email_id,
            'contactPhone' => $this->getCustomerPhone($this->contact_person_phone, $this->contact_person_phone_ext),
            'supervisor' => new UserResource(User::find($supervisorId)),
            'areaManager' => new UserResource(User::find($areaManagerId))
        ];
    }


    public function getCustomerAddress($customer) {
        return  Collection::make(
            [
                'address' => $customer->address,
                'city' => $customer->city,
                'zipCode' => $customer->postal_code
            ]
        );
    }

    public function getCustomerPhone($phone,$ext) {
        if (isset($ext)) {
            return $phone.' x'.$ext;
        } else {
            return $phone;
        }
    }
}
