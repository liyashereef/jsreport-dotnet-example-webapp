<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\IdentificationDocumentLookup;

class IdentificationDocumentLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new IncidentPriorityLookup instance.
     *
     * @param  \App\Models\IncidentPriorityLookup $incidentPriorityLookupModel
     */
    public function __construct(IdentificationDocumentLookup $identificationDocumentLookupModel)
    {
        $this->model = $identificationDocumentLookupModel;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'name', 'created_at', 'updated_at'])->get();
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
