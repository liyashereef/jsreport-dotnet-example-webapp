<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CertificateMaster;
use Modules\Admin\Models\UserCertificate;

class UserCertificateLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new PositionLookupLookupRepository instance.
     *
     * @param  \App\Models\PositionLookup $positionLookup
     */
    public function __construct(CertificateMaster $certificateMasterLookupModel,UserCertificate $userCertificate)
    {
        $this->model = $certificateMasterLookupModel;
        $this->userCertificate = $userCertificate;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'certificate_name', 'created_at', 'updated_at', 'is_deletable'])->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('certificate_name', 'asc')->pluck('certificate_name', 'id')->toArray();
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
    /**
     * Update valid Until field in Usercertificate Table, if entry is already exist, otherwise create one entry.
     *
     * @param  $data
     * @return object
     */
    public function setExpireson($data)
    {
       
        $lookup = $this->userCertificate->where('user_id', $data['user_id'])->where('certificate_id',$data['document_name_id'])->first();
        if(!is_null($lookup)){
            $lookup->expires_on = $data['document_expiry_date'];
            $lookup->save();
        }
        else{
            $lookup = new UserCertificate;
            $lookup->user_id = $data['user_id'];
            $lookup->certificate_id =  $data['document_name_id'];
            $lookup->expires_on =  $data['document_expiry_date'];
            $lookup = $lookup->save();
        }


        return $lookup;
    }
}
