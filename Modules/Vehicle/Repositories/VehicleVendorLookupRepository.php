<?php

namespace Modules\Vehicle\Repositories;

use Modules\Vehicle\Models\VehicleVendorLookup;

class VehicleVendorLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new VehicleVendorLookupRepository instance.
     *
     * @param  \App\Models\VehicleVendorLookup $vehicleVendorLookup
     */
    public function __construct(VehicleVendorLookup $vehicleVendorLookup)
    {
        $this->model = $vehicleVendorLookup;
    }

    /**
     * Get vehicle vendor lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id','vehicle_vendor','created_at','updated_at'])->get();
    }

    /**
     * Get vehicle vendor lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('vehicle_vendor', 'asc')->pluck('vehicle_vendor', 'id')->toArray();
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
