<?php

namespace Modules\EmployeeTimeOff\Repositories;

use Modules\EmployeeTimeOff\Models\EmployeeTimeoffWorkflow;
use Spatie\Permission\Models\Role;

class EmployeeTimeoffWorkflowRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $employeeTimeOff;

    /**
     * Create a new EmployeeTimeoffRepository instance.
     */
    public function __construct(EmployeeTimeoffWorkflow $employeeTimeoffWorkflow)
    {
        $this->model = $employeeTimeoffWorkflow;
    }

    /**
     * Employee workflow get
     *
     * @param empty
     * @return array
     */
    public function getRoleWorkflow($emp_role_id)
    {
        //$input = $request->all();
        $data = $this->model->where('emp_role_id',$emp_role_id)->get();
        return $data;
    }

    /**
     * Employee workflow get
     *
     * @param empty
     * @return array
     */
    public function getRoleWorkflowLevel($emp_role_id,$level)
    {
        //$input = $request->all();
        $data = $this->model->where('emp_role_id',$emp_role_id)->where('level',$level)->first();
        return $data;
    }



     /**
     * Get Workflow Employee 
     *
     * @param role_id
     * @return string
     */
    public function getRoleWorkflowApproverEmployee($role_id)
    { 
        $role_details = Role::findById($role_id);
           switch($role_details->name) {
           case 'hr_representative':
                return 'hr_id';
               break;
           case 'area_manager':
                return 'areamanager_id';
               break;
           case 'supervisor':
                return 'supervisor_id';
               break;    
           
           default:
               return 'supervisor_id';//for time being
               break;
           }             
    }



    /**
     * Employee workflow Store
     *
     * @param empty
     * @return array
     */
    public function store($request)
    {
        $input = $request->all();
        $data = $this->model->create($input);
        return $data;
    }

    /**
     * Employee workflow list
     *
     * @param empty
     * @return array
     */
    public function list($role_id)
    {
        return $this->model->where('emp_role_id',$role_id)->get();
    }

   

    /**
     * Remove the specified workflow from storage.
     *
     * @param  $ids
     * @return object
     */
    public function delete($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

}
