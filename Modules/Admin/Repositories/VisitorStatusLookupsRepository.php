<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\VisitorStatusLookups;

class VisitorStatusLookupsRepository
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
     * @param  \Modules\Admin\Models\VisitorStatusLookups $model
     */
    public function __construct(VisitorStatusLookups $model)
    {
        $this->model = $model;
    }

    /**
     * Get VisitorStatus list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * Get single VisitorStatus details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created VisitorStatus in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    { 
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified VisitorStatus from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

}
