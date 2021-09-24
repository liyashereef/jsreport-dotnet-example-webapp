<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CriteriaLookup;

class CriteriaLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CriteriaLookupRepository instance.
     *
     * @param  \App\Models\CriteriaLookup $criteriaLookup
     */
    public function __construct(CriteriaLookup $criteriaLookup)
    {
        $this->model = $criteriaLookup;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'criteria','created_at','updated_at'])->get();
    }    

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('criteria', 'asc')->pluck('criteria', 'id')->toArray();
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
