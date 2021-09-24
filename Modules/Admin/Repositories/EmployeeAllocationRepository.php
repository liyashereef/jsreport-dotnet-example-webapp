<?php

namespace Modules\Admin\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Spatie\Permission\Models\Permission;

class EmployeeAllocationRepository
{

    protected $user_repository;
    protected $role_repository;
    protected $helperService;

    /**
     * Create a new EmployeeAllocationRepository instance.
     */
    public function __construct()
    {
        $this->user_repository = new UserRepository();
        $this->role_repository = new RolesAndPermissionRepository();
        $this->helperService = new HelperService();
    }

    /**
     * Get details of employees from supervisor id
     * @param type $supervisor_id
     * @param Array $rolesOrPermissionFilter
     * @param Boolean $query_object
     * @param Boolean $customer_session
     * @param Boolean $isPermissionWise
     * @return type
     */
    public function getEmployeeAssigned(
        $supervisor_id = null,
        $rolesOrPermissionsFilter = null,
        $query_object = false,
        $customer_session = false,
        $isPermissionWise = true,
        $isActiveUsers = false
    ) {
        //validate permissions
        if($isPermissionWise){
            HelperService::verifyPermissions($rolesOrPermissionsFilter);
        }

        //load deps
        $emp_allocation_obj = EmployeeAllocation::when(($isActiveUsers == true),
        function($query) {
            $query->wherehas('user', function($q) {
                $q->where('active', 1);
            });
        }, function ($query) {
            $query->wherehas('user');
        })
            ->with(
                'user.employee',
                'user.roles.permissions',
                'user.employee.work_type',
                'user.allocation.customer',
                'user.employee.employeePosition',
                'user.securityClearanceUser.securityClearanceLookups'
            );
        //Relation string
        $relationString = $isPermissionWise ? 'user.roles.permissions' : 'user.roles';
        //filter by role or permission
        $emp_allocation_obj->when(($rolesOrPermissionsFilter !== null), function ($query)
        use ($rolesOrPermissionsFilter, $relationString) {
            $query->with($relationString);
            //dd($rolesOrPermissionsFilter);
            $query->whereHas($relationString, function ($q) use ($rolesOrPermissionsFilter,$relationString) {
                if($relationString=="user.roles"){
                    $q->select(\DB::raw("name as role_name"))->whereIn('name', $rolesOrPermissionsFilter);
                }else if($relationString=="user.roles.permissions"){
                    $q->select(\DB::raw("name as role_name"))->whereIn('name', $rolesOrPermissionsFilter);
                }

            });
        }, function ($query)use($relationString) {
            $query->with($relationString);
        });

       // dd();
        $emp_allocation_obj->when(($supervisor_id != null), function ($query) use ($supervisor_id) {
            $query->where('supervisor_id', $supervisor_id);
        });

/** START ** Get Customer Ids from Session and Filter */
    if($customer_session){
        $customer_ids = $this->helperService->getCustomerIds();

        if(!empty($customer_ids)){
                $emp_allocation_obj->whereHas('CustomerEmployeeAllocation', function ($query)
                use ($customer_ids) {
                $query->whereIn('customer_id', $customer_ids);
            });
        }
    }
/** END ** Get Customer Ids from Session and Filter */
    $emp_allocation_obj->with(['user.employee_shift_payperiods' => function ($shift_payperiod_query) {
        $shift_payperiod_query->whereHas('availableShift');
        $shift_payperiod_query->with('availableShift');
    }]);
        if ($query_object == true) {
            return $emp_allocation_obj;
        } else {
            return $emp_allocation_obj->get();
        }
    }

    /**
     * Function for user allocation
     * @param  $employee_id_list
     * @param  $supervisor_id
     * @return boolean
     */
    public function userAllocation($employee_id_list, $supervisor_id)
    {
        $selected_supervisor_emp_list = $this->getEmployeeIdAssigned($supervisor_id)->toArray();
        $allocated_employee_list = array_intersect($selected_supervisor_emp_list, $employee_id_list);
        if (!empty($allocated_employee_list)) {
            return false;
        } else {
            $this->saveAllocation($employee_id_list, $supervisor_id);
            return true;
        }
    }

    /**
     * Get Id of employees assigned to a supervisor
     *
     * @param type $supervisor_id
     * @return type
     */
    public function getEmployeeIdAssigned($supervisor_id)
    {
        return $this->getEmployeeAssigned($supervisor_id)->pluck('user_id');
    }

    /**
     * Get Id of employees assigned to a supervisor
     *
     * @param type $supervisor_id
     * @return type
     */

    public function getEmpIdAssigned($supervisor_id)
    {
        return $this->getEmployeeAssigned($supervisor_id)->pluck('user.employee.id');
    }


    /**
     * Function to save allocations
     * @param type $emp_list
     * @param type $supervisor_id
     */
    public function saveAllocation($emp_list, $supervisor_id)
    {
        foreach ($emp_list as $employee_id) {
            EmployeeAllocation::create([
                'supervisor_id' => (int) $supervisor_id,
                'user_id' => (int) $employee_id,
                'from' => Carbon::today(),
                'created_by' => \Auth::user()->id,
                'updated_by' => \Auth::user()->id,
            ]);
        }
    }

    /**
     * Get users that can report to a role
     * @param type $role
     * @return type
     */
    public function getUsersCanReportTo($role, $supervisor_id, $isPermissionWise = false)
    {
        $active = true;
        $role_reports = config('admin.role_hierarchy');
        $roleOrPermissions = [];

        if(!isset($role_reports[$role])){

            if ($roleOrPermissions) { //permission wise
                $roleOrPermissions  = $this->role_repository->getBasePermissionAsRoleArraySlugs([
                    'admin',
                    'super_admin'
                ]);
            } //role wise
        else{

                $roleOrPermissions  = $this->role_repository->getDefaultRoleListForRoleHierarchy(
                    ['admin', 'super_admin']
                );
            }
        } else if (is_string($role) &&  !empty($role_reports[$role])) {
            $roleOrPermissions = $role_reports[$role];
            $roleOrPermissions = (Permission::whereIn('name',$roleOrPermissions)->first()->roles->pluck('name'));
        } else {
            $roleOrPermissions = [];
        }

        if ($isPermissionWise) {
            //validating permissions
            HelperService::verifyPermissions($roleOrPermissions);
        }
        if(count($roleOrPermissions)>0){
            $userlist = $this->user_repository->getUserList(
                $active,
                $roleOrPermissions,
                $supervisor_id,
                null,
                false,
                $isPermissionWise
            );
        }else{
            $userlist = null;
        }

        return $userlist;
    }

    /**
     *
     * Function to prepare values for allocation table
     *
     * @param type $active
     * @param type $role
     * @param type $reporting_to
     * @return type
     */
    public function getUserAllocationList($role = null, $supervisor_id = null)
    {
        try {
            $userallocationlist = $this->getUsersCanReportTo($role, $supervisor_id,$isPermissionWise = false)
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'emp_no' => $item->employee->employee_no ?? '',
                    'emp_name' => $item->full_name,
                    'emp_email' => $item->email,
                    'emp_role' => implode(', ', array_map(array(HelperService::class, "snakeToTitleCase"), data_get($item, 'roles.*.name'))),
                    'emp_reports_to' => implode(', ', array_filter(data_get($item, 'allocatedSupervisor.*.supervisor.full_name'))),
                ];
            });
        } catch (\Throwable $th) {
            $userallocationlist = [];
        }


        return $userallocationlist;
    }

    /**
     * Function to unallocate user
     * @param  $employee_id
     * @param  $supervisor_id
     * @return boolean
     */
    public function userUnallocation($employee_id, $supervisor_id)
    {
        $emp_unallocation = EmployeeAllocation::where('user_id', $employee_id);
        if ($supervisor_id != 0) {
            $emp_unallocation->where('supervisor_id', $supervisor_id);
        }
        $unallocated = $emp_unallocation->update(['to' => Carbon::today(), 'updated_by' => \Auth::user()->id]);
        $result = $emp_unallocation->delete();
        return $result;
    }


    public function getAllocatedEmployeeList($user_model)
    {
        // if($user_model->roles[0]->name=='admin' || $user_model->roles[0]->name=='super_admin')
        $employee_arr_perm = $this->getEmployeeAssigned();
        // else
        // $employee_arr_perm = $this->getEmployeeAssigned(\Auth::user()->id);
        return $employee_arr_perm->pluck('user.full_name', 'user.id')->toArray();
    }

    /**
     * FOR APP- Check if employee is a
     * valid guard for the supervior
     *
     * @param int $employee_id
     * @param int $employee_id
     * @return bool Description
     */
    public function checkIfValid($supervisor, $employee_id)
    {
        if ($supervisor->can('supervisor')) {
            return (EmployeeAllocation::select('user_id')
                // ->whereActive(true)
                    ->where('supervisor_id', '=', $supervisor->id)
                    ->where('user_id', '=', $employee_id)
                    ->count());
        }
        return false;
    }

}
