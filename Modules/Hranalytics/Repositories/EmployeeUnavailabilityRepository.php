<?php

namespace Modules\Hranalytics\Repositories;

use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Models\EmployeeUnavailability;

class EmployeeUnavailabilityRepository
{

    protected $employeeUnavailability, $userrepository, $employeeAllocationRepository;

    /***
     *  Constructor function
     *
     */
    public function __construct(
        EmployeeUnavailability $employeeUnavailability, EmployeeAllocationRepository $employeeAllocationRepository, UserRepository $userrepository) {
        $this->model = $employeeUnavailability;
        $this->userrepository = $userrepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
    }

    /**
     * Save Unavailability.
     * @return Response
     */
    public function saveUnavailability($request)
    {
        $data['employee_id'] = $request->employee_id;
        $data['from'] = $request->from;
        $data['to'] = $request->to;
        $data['comments'] = $request->comments;
        $data['created_by'] = \Auth::user()->id;
        $result = $this->model->updateOrCreate(['id' => $request->id], $data);
        return $result->employee_id;
    }

    /**
     * List the Unavailability.
     * @return Response
     */
    public function listUnavailability($request)
    {
        $flag = false;
        $unavailability_list = $this->model->orderBy('id', 'desc')->select('from', 'to', 'id', 'comments')->where('employee_id', $request->id)->get();
        if (\Auth::user()->can('update_delete_all_employee_unavailability')) {
            $id_arr = $this->userrepository->getAllUsersID();
            $flag = in_array($request->id, $id_arr) ? true : false;

        } else if (\Auth::user()->can('update_delete_allocated_employee_unavailability')) {
            $id_arr = $this->employeeAllocationRepository->getEmployeeAssigned(\Auth::user()->id)->pluck('user_id')->toArray();
            $flag = in_array($request->id, $id_arr) ? true : false;

        }
        return $this->prepareUnavailabilityArray($unavailability_list, $flag);
    }

    /**
     * List the Unavailability.
     * @return Response
     */
    public function prepareUnavailabilityArray($unavailability_list, $flag)
    {
        $datatable_rows = array();
        foreach ($unavailability_list as $key => $each_unavailability) {
            $each_row["id"] = $each_unavailability->id;
            $each_row["from"] = $each_unavailability->from;
            $each_row["to"] = $each_unavailability->to;
            $each_row["comments"] = $each_unavailability->comments;
            $each_row["created_by"] = $each_unavailability->created_by;
            $each_row["editable"] = $flag;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function get($id)
    {
        return $this->model->find($id);

    }
    /**
     * Remove the specified Holiday from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteUnavailability($id)
    {
        return $this->model->destroy($id);
    }
}
