<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\OperationCentreEmail;

class OperationCentreEmailRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new OperationCentreEmailRepository instance.
     *
     * @param  \App\Models\OperationCentreEmail $operationCentreEmailModel
     */
    public function __construct(OperationCentreEmail $operationCentreEmailModel)
    {
        $this->model = $operationCentreEmailModel;
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'email', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display details of single Request Type
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getLookupList()
    {
        return $this->model->orderBy('email', 'asc')->pluck('email', 'id')->toArray();
    }

    /**
     * Store a newly created Request Type in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request)
    {
        $data = $request->all();
        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

    /**
     * Remove the specified Request Type from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

}
