<?php

namespace Modules\Admin\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;

class CustomerEmployeeAllocationRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $customer, $employee, $customerEmployeeAllocation;

    /**
     * Create new Repository instance.
     *
     * @param  \App\Models\Customer $customer
     * @param  \App\Models\Employee $employee
     * @param  \App\Models\CustomerEmployeeAllocation $customerEmployeeAllocation
     */
    public function __construct(Employee $employee, CustomerEmployeeAllocation $customerEmployeeAllocation, EmployeeAllocationRepository $employeeAllocationRepository, CustomerRepository $customerRepository, UserRepository $userRepository)
    {
        $this->employee = $employee;
        $this->customerEmployeeAllocation = $customerEmployeeAllocation;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->customer_repository = $customerRepository;
        $this->user_repository = $userRepository;
    }

    /**
     * Get customers list
     * @return array
     */
    public function getCustomersList()
    {
        return $this->customer_repository->getCustomersNameList(PERMANENT_CUSTOMER);
    }
    /**
     * Get customers list
     * @return array
     */
    public function getAllCustomersList()
    {
        return $this->customer_repository->getAllCustomersNameList();
    }

    /**
     * Get allocation list
     * @return object
     */
    public function allocationList($customer_id = null, $role = null, $role_except = false, $has_allocation = false)
    {
        return $this->user_repository->allocationUserList($customer_id, $role, $role_except, $has_allocation, false);
    }

    /**
     * Allocate the employee to the customer.
     * @param  $employee_id_list, $customer_id
     * @return boolean
     */
    public function allocateEmployee($employee_id_list, $customer_id, $request)
    {

        $selected_customer_employee_list = $this->customerEmployeeAllocation->where('customer_id', $customer_id)->pluck('user_id')->toArray();
        $allocated_employee_list = array_intersect($selected_customer_employee_list, $employee_id_list);
        if ($request->project_deployed != null) {
            $from = $request->get('project_deployed');
        } else {
            $from = date('Y-m-d');
        }
        if (!empty($allocated_employee_list)) {
            return false;
        } else {
            foreach ($employee_id_list as $employee_id) {
                $this->customerEmployeeAllocation->create([
                    'customer_id' => $customer_id,
                    'user_id' => $employee_id,
                    'created_by' => \Auth::user()->id,
                    'updated_by' => \Auth::user()->id,
                    'from' => $from,
                ]);
                Cache::forget('clientAppCustomerAllocation' . $employee_id);
            }
            return true;
        }
    }

    /**
     * Unallocate the employee.
     * @param  $selected_customer_id, $employee_id
     * @return boolean
     */
    public function unallocateEmployee($selected_customer_id, $employee_id)
    {
        $employee_unallocation = $this->customerEmployeeAllocation->where('user_id', $employee_id);
        if ($selected_customer_id != 0) {
            $employee_unallocation->where('customer_id', $selected_customer_id);
        }
        $employee_unallocation->update(['to' => date('Y-m-d'), 'updated_by' => \Auth::user()->id]);
        $unallocated = $employee_unallocation->delete();
        return $unallocated;
    }

    /**
     * Get Permanent/STC customers allocated
     * @param type $arr_user
     * @return type
     */
    public function getAllocatedCustomerId($arr_user, $stc = false)
    {
        return data_get($this->customerEmployeeAllocation->with(['customer' => function ($query) use ($stc) {
            return $query->where('stc', $stc);
        }])->whereIn('user_id', $arr_user)->get()->toArray(), "*.customer.id");
    }

    /**
     * Get all permanent customers for a given user
     * @param User $user_model
     * @return Array $customers_list - Customer id array
     */
    public function getAllocatedPermanentCustomers($user_model, $is_shift_enabled = null)
    {
        $customers_list = array();
        if ($user_model->hasAnyPermission(['view-all-permanent-customer', 'admin', 'super_admin'])) {
            $customers_list = array_keys($this->customer_repository->getList(PERMANENT_CUSTOMER, $is_shift_enabled));
        } else {
            /**
             * Customer list -- direct & assigned employee alocated customer list.
             * $allocated_user = $this->employeeAllocationRepository->getEmployeeAssigned([$user_model->id]);
             * $customers_list = $this->getAllocatedCustomerId(array_merge([$user_model->id], $allocated_user->pluck('user_id')->toArray()), false);
             */
            $customers_list = $this->getAllocatedCustomerId([$user_model->id], false);
        }
        return $customers_list;
    }
    public function getUserallocatedcustomers($flag = null)
    {

        return $this->customerEmployeeAllocation->with(['customer'])->where('user_id', \Auth::user()->id)->get();
    }
    public function getAllocatedStcCustomers($user_model, $is_shift_enabled = null)
    {
        $customers_list = array();
        $user = Auth::user();
        if (auth()->user()->hasAnyPermission(['view-all-stc-customer', 'super_admin', 'admin'])) {
            $customers_list = array_keys($this->customer_repository->getList(STC_CUSTOMER, $is_shift_enabled));
        } else {
            /**
             * Customer list -- direct & assigned employee alocated customer list.
             *$allocated_user = $this->employeeAllocationRepository->getEmployeeAssigned([$user_model->id]);
             *$customers_list = $this->getAllocatedCustomerId(array_merge([$user_model->id], $allocated_user->pluck('user_id')->toArray()), true);
             */
            $customers_list = $this->getAllocatedCustomerId([$user_model->id], true);
        }
        return $customers_list;
    }

    public function getAllocatedCustomers($user_model)
    {
        $customer_arr_perm = $this->getAllocatedPermanentCustomers($user_model);
        $customer_arr_stc = $this->getAllocatedStcCustomers($user_model);
        return array_unique(array_merge($customer_arr_perm, $customer_arr_stc));
    }

    // public function getAllocatedCustomersList($user_model)
    // {
    //     $customer_arr_perm = $this->getAllocatedPermanentCustomers($user_model);

    //     $customer_arr_stc = $this->getAllocatedStcCustomers($user_model);
    //     return array_unique(array_merge($customer_arr_perm, $customer_arr_stc));
    // }
    public function getAllocatedCustomersList($user_model)
    {
        $customer_arr_perm = $this->getAllocatedPermanentCustomers($user_model);

        $customer_arr_stc = $this->getAllocatedStcCustomers($user_model);
        $allocated_customers_arr = array_unique(array_merge($customer_arr_perm, $customer_arr_stc));
        $customer_details_arr = $this->customer_repository->getCustomers($allocated_customers_arr);
        return $customer_details_arr->pluck('customer_name_and_number', 'id')->toArray();
    }

    /**
     * Get Direct Allocated Customers
     * @param  $user_model, $is_shift_enabled
     * @return array
     *
     */
    public function getDirectAllocatedCustomersList($user_model)
    {
        $customer_arr_perm = $this->getDirectAllocatedPermanentCustomers($user_model);
        $customer_arr_stc = $this->getDirectAllocatedStcCustomers($user_model);
        $allocated_customers_arr = array_unique(array_merge($customer_arr_perm, $customer_arr_stc));
        $customer_details_arr = $this->customer_repository->getCustomers($allocated_customers_arr);
        return $customer_details_arr->pluck('customer_name_and_number', 'id')->toArray();
    }
    /**
     * Get Direct Allocated Permananet Customers
     * @param  $user_model, $is_shift_enabled
     * @return array
     *
     */
    public function getDirectAllocatedPermanentCustomers($user_model, $is_shift_enabled = null)
    {
        $customers_list = array();
        if ($user_model->hasAnyPermission(['admin', 'super_admin'])) {
            $customers_list = array_keys($this->customer_repository->getList(PERMANENT_CUSTOMER, $is_shift_enabled));
        } else {
            $customers_list = $this->getAllocatedCustomerId([$user_model->id], false);
        }
        return $customers_list;
    }

    /**
     * Get Direct Allocated STC Customers
     * @param  $user_model, $is_shift_enabled
     * @return array
     *
     */
    public function getDirectAllocatedStcCustomers($user_model, $is_shift_enabled = null)
    {
        $customers_list = array();
        $user = Auth::user();
        if (auth()->user()->hasAnyPermission(['super_admin', 'admin'])) {
            $customers_list = array_keys($this->customer_repository->getList(STC_CUSTOMER, $is_shift_enabled));
        } else {
            $customers_list = $this->getAllocatedCustomerId([$user_model->id], true);
        }
        return $customers_list;
    }

    /**
     * Get Direct Allocated STC Customers
     * @param  $user_model, $is_shift_enabled
     * @return array
     *
     */
    public function getDirectAllocatedCustomers($user_model)
    {
        $customer_arr_perm = $this->getDirectAllocatedPermanentCustomers($user_model);
        $customer_arr_stc = $this->getDirectAllocatedStcCustomers($user_model);
        return array_unique(array_merge($customer_arr_perm, $customer_arr_stc));
    }

    /**
     * Get Shift Journal Customers
     * @param  $user_model, $is_shift_enabled
     * @return array
     *
     */
    public function getGuardTourCustomers($user_model)
    {
        $customers_list = array();
        if ($user_model->hasAnyPermission(['view_all_guard_tour', 'view_all_shift_journal'])) {
            $customers_list = array_keys($this->customer_repository->getGuardTourCustomerList());
        } else {
            $allocated_user = $this->employeeAllocationRepository->getEmployeeAssigned([\Auth::user()->id]);
            //$customers_list = $this->getAllAllocatedCustomerId(array_merge([\Auth::user()->id], $allocated_user->pluck('user_id')->toArray()));
            $customers_list = $this->getAllAllocatedCustomerId([Auth::user()->id]);
        }
        return $customers_list;
    }

    /**
     * Get all allocated and guard tour enabled customers
     * @param type $arr_user
     * @return type
     */
    public function getAllAllocatedGuardTourCustomerId($arr_user, $guard_tour_enabled = true)
    {
        return data_get($this->customerEmployeeAllocation->with(['customer' => function ($query) use ($guard_tour_enabled) {
            return $query->where('guard_tour_enabled', $guard_tour_enabled);
        }])->whereIn('user_id', $arr_user)->get()->toArray(), "*.customer.id");
    }

    /**
     * Get Permanent & STC customers allocated
     * @param type $arr_user
     * @return type
     */
    public function getAllAllocatedCustomerId($arr_user)
    {
        return data_get($this->customerEmployeeAllocation->with(['customer'])->whereIn('user_id', $arr_user)->get()->toArray(), "*.customer.id");
    }

    /**
     * Get All Employee List which allocated to my Customer and me
     * me = user_id
     *
     *@param $customer_id
     *@param Array $role,$user_id
     */
    public function getAllocatedUsers($role = null, $user_id = null)
    {

        $customerIds = array();
        $user_allocation_list = $this->user_repository->getUserList(1, $role, $user_id, null, false, false);

        if ($user_id != null) {
            $user = User::find($user_id);
            $customerIds = $this->getAllocatedCustomers($user);
        }

        $customer_user_allocation_list = $this->allocationList($customerIds, $role, false);

//        return array_unique(array_merge($user_allocation_list,$customer_user_allocation_list),SORT_REGULAR);

        return $data = $user_allocation_list->merge($customer_user_allocation_list);
    }

    /**
     * For mobile App
     * To get Customer allocated users and user allocated users
     *
     *@param $customer_id, $role,$user_id
     */
    public function getUserAndCustomerAllocatedUsers($customer_id = null, $user_id = null, $role = null)
    {
        $customerIds = array();
        if ($user_id != null) {
            $userid = $user_id;
        } else {
            $userid = \Auth::user()->id;
        }
        $user_allocation_list = $this->user_repository->getUserList(1, $role, $userid);
        if ($customer_id) {
            $customerIds = $customer_id;
        } else {
            $user = User::find($userid);
            $customerIds = $this->getAllocatedCustomers($user);
        }
        $customer_user_allocation_list = $this->allocationList($customerIds, $role, false);
        $user_data = $user_allocation_list->intersect($customer_user_allocation_list);
        $data = $user_data->pluck('name_with_emp_no', 'id')->toArray();
        $user_array = [];
        foreach ($data as $key => $value) {
            $object = new \stdClass();
            $object->id = $key;
            $object->name = $value;
            $user_array[] = $object;
        }
        return $user_array;
    }
}
