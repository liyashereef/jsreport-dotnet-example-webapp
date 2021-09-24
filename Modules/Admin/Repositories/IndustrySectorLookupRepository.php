<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IndustrySectorLookup;

class IndustrySectorLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new IndustrySectorLookupLookupRepository instance.
     *
     * @param  \App\Models\IndustrySectorLookup $industrySectorLookup
     */
    public function __construct(IndustrySectorLookup $industrySectorLookupModel)
    {
        $this->model = $industrySectorLookupModel;
    }     

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('industry_sector_name','asc')->select(['id', 'industry_sector_name','created_at','updated_at'])->get();
    }  
    
    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('industry_sector_name','asc')->pluck('industry_sector_name','id')->toArray();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);        
    }

    /**
     * Remove the specified Security Clearance from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
