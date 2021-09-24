<?php

namespace Modules\Timetracker\Repositories;

use Modules\Timetracker\Models\EmployeeShiftApprovalLog;

class EmployeeShiftApprovalLogRepository
{
    protected $model;

    public function __construct(EmployeeShiftApprovalLog $model)
    {
        $this->model = $model;
    }

    public function getByEmployeeShiftPayperiodId($id)
    {
        return $this->model->where('employee_shift_payperiod_id','=',$id);
    }

     public function store($inputs)
    {
        return $this->model->create($inputs);
    }

    public function getAllBy($filter = null){

        return $this->model
        ->where(function($query) use ($filter){
         if(isset($filter['employee_shift_payperiod_id']) && !empty($filter['employee_shift_payperiod_id'])){
             $query->where('employee_shift_payperiod_id',$filter['employee_shift_payperiod_id']);
            }
        })->orderBy('created_at', 'DESC')
        ->with('approved_user')
        ->get();
    }


}
