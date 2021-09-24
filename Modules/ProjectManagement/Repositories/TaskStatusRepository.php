<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\ProjectManagement\Models\PmTaskStatus;

class TaskStatusRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(PmTaskStatus $task)
    {
        $this->model = $task;
    }


    /**
     * Get all Tasks of a project
     */
    public function getByTask($groupId)
    {
        return $this->model
            ->where('task_id', '=', $groupId)
            ->get();
    }

     /**
     * Get all Tasks of a project
     */
    public function getMaxPercentage($taskId)
    {
        return $this->model
            ->where('task_id', '=', $taskId)
             ->orderBy('percentage', 'desc')->first();
        
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created WorkType in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)

    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified WorkType from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteTaskStatus($id)
    {
        return $this->model->destroy($id);
    }


    /**
     * Store a newly created WorkType in storage.
     *
     * @param  $request
     * @return object
     */

    public function saveStatus($task_id,$percentage=0,$notes=null,$id=null)

    {
                $data['task_id']=$task_id;
                $data['percentage']=$percentage;
                $data['notes']=$notes;
                $data['status_date']=\Carbon::now();
                $data['id']=$id;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function deleteTaskStatusByPercentage($taskId)
    {

       return $this->model->where('task_id',$taskId)->where('percentage',100)->delete();
    }
}