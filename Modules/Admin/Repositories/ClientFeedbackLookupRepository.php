<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ClientFeedbackLookup;

class ClientFeedbackLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new ClientFeedbackLookup instance.
     *
     * @param  \App\Models\ClientFeedbackLookup $clientFeedbackLookup
     */
    public function __construct(ClientFeedbackLookup $clientFeedbackLookup)
    {
        $this->model = $clientFeedbackLookup;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'feedback', 'is_editable', 'short_name', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('feedback', 'asc')->pluck('feedback', 'id')->toArray();
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
