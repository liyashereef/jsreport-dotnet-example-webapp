<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MobileSecurityPatrolSubject;

class MobileSecurityPatrolSubjectRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new MobileSecurityPatrolSubject instance.
     *
     * @param  \App\Models\MobileSecurityPatrolSubject $mobileSecurityPatrolSubject
     */
    public function __construct(MobileSecurityPatrolSubject $mobileSecurityPatrolSubject)
    {
        $this->model = $mobileSecurityPatrolSubject;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'subject', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('subject', 'asc')->pluck('subject', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
