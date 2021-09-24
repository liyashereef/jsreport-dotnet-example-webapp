<?php

namespace Modules\SupervisorPanel\Repositories;

use Modules\Supervisorpanel\Models\CustomerReportAdhoc;

class CustomerReportAdhocRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $customer_report_adhoc;

    /**
     * Create a new CustomerReportAdhocRepository instance. 
     * 
     */
    public function __construct(CustomerReportAdhoc $customer_report_adhoc)
    {
        $this->model = $customer_report_adhoc;
    }

    /**
     * Employee leave Store
     *
     * @param empty
     * @return array
     */
    public function store($employeeLeaveData, $payperiod_id, $customerpayperiod_id)
    {
        $customerReportAdhocStore = $this->model->create([
            'employee_id' => $employeeLeaveData[0],
            'date' => $employeeLeaveData[1],
            'hours_off' => $employeeLeaveData[2],
            'reason_id' => $employeeLeaveData[3],
            'notes' => $employeeLeaveData[4],
            'payperiod_id' => $payperiod_id,
            'customer_payperiod_template_id' => $customerpayperiod_id,
        ]);
        return $customerReportAdhocStore;
    }

    /**
     * Remove the specified Employee Leave from storage.
     *
     * @param  $ids
     * @return object
     */
    public function delete($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

}
