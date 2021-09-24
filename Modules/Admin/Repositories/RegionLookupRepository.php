<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\RegionLookup;

class RegionLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RegionLookupLookupRepository instance.
     *
     * @param  \App\Models\RegionLookup $RegionLookup
     */
    public function __construct(RegionLookup $regionLookupModel)
    {
        $this->model = $regionLookupModel;
    }


    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('region_name','asc')->pluck( 'region_name','id')->toArray();
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('region_name','asc')->select(['id', 'region_name','created_at','updated_at'])->get();
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

    public function getAllRegionDescription(){
        $regionDescription= $this->model->select(['id', 'region_description'])->get();
        $regionCollect=collect($regionDescription);
        $grouped = $regionCollect->mapToGroups(function ($item, $key) {
            return [$item['id'] => $item['region_description']];
            });
        return $grouped->toArray();
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
