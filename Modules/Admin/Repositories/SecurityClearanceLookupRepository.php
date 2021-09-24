<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\SecurityClearanceLookup;
use Modules\Admin\Models\SecurityClearanceUser;

class SecurityClearanceLookupRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $securityClearanceLookupModel, $securityClearanceUserModel;

    /**
     * Create a new SecurityClearanceLookupRepository instance.
     *
     * @param  \App\Models\SecurityClearanceLookup $securityClearanceLookupModel
     * @param  \App\Models\SecurityClearanceUser $securityClearanceUserModel
     */
    public function __construct(SecurityClearanceLookup $securityClearanceLookupModel, SecurityClearanceUser $securityClearanceUserModel)
    {
        $this->securityClearanceLookupModel = $securityClearanceLookupModel;
        $this->securityClearanceUserModel = $securityClearanceUserModel;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->securityClearanceLookupModel->select(['id', 'security_clearance', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->securityClearanceLookupModel->orderBy('security_clearance')->pluck('security_clearance', 'id')->toArray();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        $singleRecord = $this->securityClearanceLookupModel->find($id);
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
        $lookup = $this->securityClearanceLookupModel->updateOrCreate(array('id' => $data['id']), $data);
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
        $lookup_delete = $this->securityClearanceLookupModel->destroy($id);
        $security_clearance_user_delete = $this->securityClearanceUserModel->where('security_clearance_lookup_id', $id)->delete();
        return $lookup_delete;
    }

     /**
     * Update valid Until field in SecurityClearanceUser Table, if entry is already exist, otherwise create one entry.
     *
     * @param  $data
     * @return object
     */
   public function setValiduntil($data)
    {
       
        $lookup = $this->securityClearanceUserModel->where('user_id', $data['user_id'])->where('security_clearance_lookup_id',$data['document_name_id'])->first();
        if(!is_null($lookup)){
            $lookup->valid_until = $data['document_expiry_date'];
            $lookup->save();
        }
        else{
            $lookup = new SecurityClearanceUser;
            $lookup->user_id = $data['user_id'];
            $lookup->security_clearance_lookup_id =  $data['document_name_id'];
            $lookup->valid_until =  $data['document_expiry_date'];
            $lookup = $lookup->save();
        }



        return $lookup;
    }
 
}
