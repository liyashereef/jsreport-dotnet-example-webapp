<?php

namespace Modules\EmployeeTimeOff\Repositories;

use Modules\EmployeeTimeOff\Models\TimeOffLog;

class TimeOffLogRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $timeOffLog;

    /**
     * Create a new EmployeeTimeoffRepository instance.
     */
    public function __construct(TimeOffLog $timeOffLog)
    {
        $this->model = $timeOffLog;
    }

    /**
     * Employee leave Store
     *
     * @param empty
     * @return array
     */
    public function store($request)
    {
        //$input = $request->all();  
        $data['time_off_id'] = $request['id'];
        $data['approved'] = $request['approved'];
        $data['start_date'] = $request['start_date'];
        $data['end_date'] = $request['end_date'];
        $data['days_approved'] = $request['days_approved'];
        $data['days_rejected'] = $request['days_rejected'];
        $data['days_remaining'] = $request['days_remaining'];
        $data['created_by'] = \Auth::id();
        return $this->model->create($data);
         
    }

    /**
     * Employee leave list
     *
     * @param empty
     * @return array
     */
    public function list()
    {
        return  $this->model->with(['employee.user','leave_reason'])->get();
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
