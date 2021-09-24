<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\ProjectManagement\Models\PmGroup;

class GroupRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\WorkType $workTypeModel
     */
    public function __construct(PmGroup $pmGroup)
    {
        $this->model = $pmGroup;
    }

    /**
     * Get all Groups
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'name', 'project_id','created_at'])->get();
    }

    /**
     * Get  WorkType list
     *
     * @param empty
     * @return array
     */
    public function getGroupNames($project_list)
    {
        //return $this->model->select('name','id','customer_id')->get()->toArray();
        return $this->model->whereIn('project_id', $project_list)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    /**
     * Get all Groups of a project
     */
    public function getByProject($projectId)
    {
        return $this->model->select(['id', 'name', 'project_id'])
            ->where('project_id', '=', $projectId)
            ->get();
    }


    public function get($id)
    {
        return $this->model->with('tasks')->find($id);
    }


    public function save($data)
    {
        $data['name']=strip_tags($data['name']);
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function deleteGroup($id)
    {
        if (is_array($id)) {
            return $this->model->whereIn('id', $id)->delete();
        } else {
            return $this->model->destroy($id);
        }
    }
}
