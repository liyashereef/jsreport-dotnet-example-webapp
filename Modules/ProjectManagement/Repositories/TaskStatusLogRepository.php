<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\ProjectManagement\Models\PmTaskStatusLog;

class TaskStatusLogRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(PmTaskStatusLog $statusLog)
    {
        $this->model = $statusLog;
    }


    /**
     * Get all Tasks of a project
     */
    public function getByTask($taskId)
    {
        return $this->model
            ->where('task_id', '=', $taskId)
            ->get();
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
    public function deleteTaskStatusLog($id)
    {
        return $this->model->destroy($id);
    }
}
