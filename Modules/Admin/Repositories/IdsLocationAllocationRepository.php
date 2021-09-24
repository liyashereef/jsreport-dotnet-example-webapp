<?php

namespace Modules\Admin\Repositories;

use App\Services\HelperService;

use Modules\Admin\Models\IdsLocationAllocation;
use Modules\Admin\Models\User;

class IdsLocationAllocationRepository
{

    protected $model, $helperService;

    /**
     * Create a new UserRepository instance.
     *
     * @param  \App\Models\User $user
     */
    public function __construct()
    {
        $this->ids_location_allocation = new IdsLocationAllocation();
        $this->userModel = new User();
        $this->helperService = new HelperService();
    }

     /*
     * Get allocation user list
     * @param $customer_id
     *@param Array $role,$role_except(Is role to be excluded=false)
     */
    public function allocationList($location_id=null, $has_allocation=false)
    { 
        $get_employees = $this->userModel->whereActive(true)
            ->when($has_allocation, function ($q) {
                   $q->whereHas('idsLocationAllocation');
             })
            ->with('employee_profile', 'employee_profile.work_type', 'roles', 'idsLocationAllocation.IdsOffice')
            ->whereHas('roles', function ($query)  {
                $query->whereNotIn('roles.name', ['super_admin', 'admin']);
            });
            if ($location_id != null) {
            $get_employees->whereHas('idsLocationAllocation', function ($query) use ($location_id) {
                if (is_array($location_id)) {
                    $query->whereIn('ids_office_id', $location_id);
                } else {
                    $query->where('ids_office_id', $location_id);
                }
            });
        }
        return $get_employees->get();
    }

     
    /**
     * Allocate the employee to the customer.
     * @param  $employee_id_list, $customer_id
     * @return boolean
     */
    public function allocateEmployee($employee_id_list, $ids_office_id, $request)
    {

        $selected_customer_employee_list = $this->ids_location_allocation->where('ids_office_id', $ids_office_id)->pluck('user_id')->toArray();
        $allocated_employee_list = array_intersect($selected_customer_employee_list, $employee_id_list);
        
        if (!empty($allocated_employee_list)) {
            return false;
        } else {
            foreach ($employee_id_list as $employee_id) {
                $this->ids_location_allocation->create([
                    'ids_office_id' => $ids_office_id,
                    'user_id' => $employee_id,
                    'created_by' => \Auth::user()->id,
                    'updated_by' => \Auth::user()->id,
                ]);
            }
            return true;
        }
    }

    /**
     * Unallocate the employee.
     * @param  $selected_ids_office_id, $user_id
     * @return boolean
     */
    public function unallocateEmployee($selected_ids_office_id, $user_id)
    {
        $employee_unallocation = $this->ids_location_allocation->where('user_id', $user_id);
        if ($selected_ids_office_id != 0) {
            $employee_unallocation->where('ids_office_id', $selected_ids_office_id);
        }
        $employee_unallocation->update(['updated_by' => \Auth::user()->id]);
        $unallocated = $employee_unallocation->delete();
        return $unallocated;
    }

    public function getByUserLocations($user_id){
        return $this->ids_location_allocation
        ->where('user_id', $user_id)
        ->pluck('ids_office_id')
        ->toArray();
    }

    public function unallocateByOfficeId($ids_office_id)
    {
        $employee_unallocation = $this->ids_location_allocation;
        $employee_unallocation->where('ids_office_id', $ids_office_id);
        $employee_unallocation->update(['updated_by' => \Auth::user()->id]);
        $unallocated = $employee_unallocation->delete();
        return $unallocated;
    }

}