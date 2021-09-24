<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\VisitorLogCustomerTemplateAllocation;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;

class CustomerTemplateAllocationRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $customer, $employee, $customerEmployeeAllocation, $visitor_log_repository, $visitorLogCustomerTemplateAllocation;

    /**
     * Create new Repository instance.
     *
     * @param  \App\Models\Customer $customer
     * @param  \App\Models\Employee $employee
     * @param  \App\Models\CustomerEmployeeAllocation $customerEmployeeAllocation
     */
    public function __construct(Employee $employee, CustomerEmployeeAllocation $customerEmployeeAllocation, EmployeeAllocationRepository $employeeAllocationRepository, CustomerRepository $customerRepository, UserRepository $userRepository, VisitorLogTemplateRepository $visitor_log_repository, VisitorLogCustomerTemplateAllocation $visitorLogCustomerTemplateAllocation)
    {
        $this->employee = $employee;
        $this->customerEmployeeAllocation = $customerEmployeeAllocation;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->customer_repository = $customerRepository;
        $this->user_repository = $userRepository;
        $this->visitor_log_repository = $visitor_log_repository;
        $this->visitorLogCustomerTemplateAllocation = $visitorLogCustomerTemplateAllocation;
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
     * Get allocation list
     * @return object
     */
    public function allocationList($customer_id = null)
    {
        $allocationList = $this->visitor_log_repository->allocationTemplateList($customer_id);
        return $this->prepareDataforTemplateAllocation($allocationList);
    }

    /**
     * Prepare datatable elements as array.
     * @param  $allocationList
     * @return array
     */
    public function prepareDataforTemplateAllocation($allocationList)
    {
        $datatable_rows = array();
        foreach ($allocationList as $key => $each_list) {
            $each_row["project"] = null;
            $each_row["client_name"] = null;
            $each_row["id"] = $each_list->id;
            $each_row["template_name"] = $each_list->template_name;
            foreach ($each_list->visitorLogTemplate as $key => $value) {
                $each_row["project"][$key] = $value->customer->client_name;
                $each_row["client_name"] = implode(",", $each_row["project"]);
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Allocate the employee to the customer.
     * @param  $employee_id_list, $customer_id
     * @return boolean
     */
    public function allocateEmployee($template_id_list, $customer_id, $request)
    {

        $selected_customer_template_list = $this->visitorLogCustomerTemplateAllocation->where('customer_id', $customer_id)->pluck('template_id')->toArray();
        $allocated_template_list = array_intersect($selected_customer_template_list, $template_id_list);
        if ($request->project_deployed != null) {
            $from = $request->get('project_deployed');
        } else {
            $from = date('Y-m-d');
        }
        if (!empty($allocated_template_list)) {
            return false;
        } else {
            foreach ($template_id_list as $template_id) {
                $this->visitorLogCustomerTemplateAllocation->create([
                    'customer_id' => $customer_id,
                    'template_id' => $template_id,
                ]);
            }
            return true;
        }
    }

    /**
     * Unallocate the employee.
     * @param  $selected_customer_id, $employee_id
     * @return boolean
     */
    public function unallocateTemplate($selected_customer_id, $template_id)
    {
        $template_unallocation = $this->visitorLogCustomerTemplateAllocation->where('template_id', $template_id);
        if ($selected_customer_id != 0) {
            $template_unallocation->where('customer_id', $selected_customer_id);
        }
        $unallocated = $template_unallocation->delete();
        return $unallocated;
    }

}
