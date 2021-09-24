<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecSecurityClearanceLookup;

class RecSecurityClearanceLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $recSecurityClearanceLookupModel;

    /**
     * Create a new SecurityClearanceLookupRepository instance.
     *
     * @param  Modules\Recruitment\Models\RecSecurityClearanceLookup $recSecurityClearanceLookupModel
     */
    public function __construct(RecSecurityClearanceLookup $recSecurityClearanceLookupModel)
    {
        $this->model = $recSecurityClearanceLookupModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'security_clearance', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('security_clearance')->pluck('security_clearance', 'id')->toArray();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $singleRecord = $this->model->find($id);
        return $singleRecord;
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $lookup = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $lookup;
    }

    /**
     * Remove the specified Security Clearance from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $lookup_delete = $this->model->destroy($id);
        return $lookup_delete;
    }

}
