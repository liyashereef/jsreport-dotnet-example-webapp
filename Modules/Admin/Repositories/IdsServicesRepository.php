<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsOfficeServiceAllocation;
use Modules\Admin\Models\IdsServices;
use Modules\Admin\Models\IdsPassportPhotoService;

class IdsServicesRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  Modules\Admin\Models\IdsServices $idsServices
     */
    public function __construct(IdsServices $idsServices,IdsPassportPhotoService $idsPassportPhotoService)
    {
        $this->model = $idsServices;
        $this->passportPhotoModel = $idsPassportPhotoService;
    }

    /**
     * Get  service list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
       return $this->model
       ->with([
          'IdsOfficeServiceAllocation'=>function($query){
            $query->select('id','ids_service_id','ids_office_id');
          },
          'IdsOfficeServiceAllocation.IdsOffice'=>function($query){
            $query->select('id','name');
          },
       ])
       ->get();
    }

    public function getAllServices(){
        return $this->model->all();
    }

     /**
     * Get all service by office.
     *
     * @param empty
     * @return array
     */

    public function getByOffice($officeId){
        return $this->model
        ->whereHas('IdsOfficeServiceAllocation', function($query) use($officeId){
            return $query->where('ids_office_id',$officeId);
        })
        ->orderBy('name')
        ->select('id','name','rate','tax_master_id','description','is_photo_service','is_photo_service_required')
        ->with(['taxMaster'=> function($query){
            return $query->select('id','name','short_name');
        },
        'taxMaster.taxMasterLog'=> function($query){
            return $query->select('id','tax_master_id','tax_percentage','effective_from_date','effective_end_date');
        }
        ])

        ->get();
    }

     /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        $result = $this->model->with('taxMaster','taxMaster.taxMasterLog')->find($id);
        $result->office_ids = [];
        if($result){
            $result->office_ids = IdsOfficeServiceAllocation::where('ids_service_id',$result->id)->pluck('ids_office_id')->toArray();
        }

         return $result;
    }

     /**
     * Store a newly created service in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs){
        $inputs['is_photo_service'] = isset($inputs['is_photo_service']) ? 1 : 0;
        $inputs['is_photo_service_required'] = isset($inputs['is_photo_service_required']) ? 1 : 0;
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Get single service details
     *
     * @param $id
     * @return object
     */
    public function destroy($id){
        return $this->model->find($id)->delete();
    }

    public function getAllPassportPhotoServices()
    {
        return $this->passportPhotoModel->get();
    }


}
