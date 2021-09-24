<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\DepartmentMaster;
use Modules\Admin\Models\DepartmentEmployees;

class DepartmentMasterRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $departmentEmployees;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\CustomerType $model
     */
    public function __construct(DepartmentMaster $model, DepartmentEmployees $departmentEmployees)
    {
        $this->model = $model;
        $this->departmentEmployees = $departmentEmployees;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */


    public function getAll()
    {

        $data = $this->model->with('employeeMapping')->get();
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            $each_row["id"] =   $each_record->id;
            $each_row["name"] = $each_record->name;
            if ($each_record->allocated_regionalmanager == 1) {
                $each_row["allocated_regionalmanager"] = 'Yes';
            } else {
                $each_row["allocated_regionalmanager"] = 'No';
            }
            if ($each_record->allocated_supervisor == 1) {
                $each_row["allocated_supervisor"] = 'Yes';
            } else {
                $each_row["allocated_supervisor"] = 'No';
            }
            $each_row["emp_allocation"] = implode(', ', array_filter(data_get($each_record, 'employeeMapping.*.user.full_name')));
            $employee = data_get($each_record, 'employeeMapping.*');
            $user_arr['employees'] = [];
            foreach ($employee as $key => $each_user_id) {
                if (isset($each_user_id->user) && $each_user_id->user->first_name) {
                    array_push($user_arr['employees'], $each_user_id->user->first_name . " " . $each_user_id->user->last_name);
                }
            }
            $latest_user_data = $user_arr;
            $combined_result = $each_row + $latest_user_data;
            array_push($datatable_rows, $combined_result);
        }

        return $datatable_rows;
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('employeeMapping')->find($id);
    }

    public function getDepartments()
    {
        $departmentArray = [];
        $departments = $this->model->orderBy("name", "asc")->get();
        foreach ($departments as $department) {
            $departmentArray[] = ["id" => $department->id, "name" => $department->name];
        }
        return $departmentArray;
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function save($inputs)
    {
        $allocatedEmployees = isset($inputs['employee']) ? $inputs['employee'] : null;
        $inputs['allocated_regionalmanager'] = isset($inputs['allocated_regionalmanager']) ? 1 : 0;
        $inputs['allocated_supervisor'] = isset($inputs['allocated_supervisor']) ? 1 : 0;
        $department = $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
        $data['department_master_id'] = $department->id;
        $this->departmentEmployees->where('department_master_id', $data['department_master_id'])->delete();
        if ($allocatedEmployees != null) {
            foreach ($inputs['employee'] as $key => $employee) {
                $allocation = $this->departmentEmployees->create(['department_master_id' => $data['department_master_id'], 'user_id' => $inputs['employee'][$key]]);
            }
        }
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    public function getList()
    {
        return $this->model->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }
}
