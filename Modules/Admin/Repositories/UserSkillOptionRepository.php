<?php

namespace Modules\Admin\Repositories;

use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\UserSkillOption;

class UserSkillOptionRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new bank instance.
     *
     * @param  \App\Models\UserSalutations $userSalutations
     */
    public function __construct(UserSkillOption $userSkillOption)
    {
        $this->model = $userSkillOption;
    }

    public function getAll()
    {
        return $this->model->select(['id', 'name', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display details of single Skill Name
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Skill in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if (!isset($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        }
        $data['updated_by'] = Auth::user()->id;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the Skill from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
