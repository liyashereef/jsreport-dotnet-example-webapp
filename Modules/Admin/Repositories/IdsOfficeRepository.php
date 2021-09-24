<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsOffice;
use Modules\Admin\Repositories\IdsLocationAllocationRepository;

class IdsOfficeRepository
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
     * @param  Modules\Admin\Models\IdsOffice $idsOffice
     */
    public function __construct(IdsOffice $idsOffice,
    IdsLocationAllocationRepository $idsLocationAllocationRepository)
    {
        $this->model = $idsOffice;
        $this->idsLocationAllocationRepository = $idsLocationAllocationRepository;
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
       return $this->model->orderBy('name')
       ->with('IdsOfficeTimings')
       ->get();
    }

    /**
     * Get offfice list public side
     *
     * @param empty
     * @return array
     */

    public function getOffices(){
        return $this->model->orderby('name')->get();
     }


     /**
     * Get offfice name and id
     *
     * @param empty
     * @return array
     */

    public function getNameAndId(){
        return $this->model->pluck('name','id')->toArray();
     }

    /**
     * Get offfice name and id
     *
     * @param empty
     * @return array
     */

    public function getByIds($ids){
        return $this->model->whereIn('id',$ids)->pluck('name','id')->toArray();
     }

    /**
     * Get offfice name and id based on permission.
     *
     * @param empty
     * @return array
     */

     public function getPermissionBaseLocation($withAddress = true){

        $oficeId = null;
        $permissionAll = false;
        if(\Auth::user()->hasPermissionTo('ids_view_allocated_locaion_schedule')){
            $permissionAll = false;
            $oficeId = $this->idsLocationAllocationRepository->getByUserLocations(\Auth::id());
        }

        if(\Auth::user()->hasPermissionTo('ids_view_all_schedule')){
            $permissionAll = true;
        }
        if($withAddress){
            return $this->model
            ->orderBy('name')
            ->when($permissionAll == false, function ($query) use($oficeId){
                 $query->whereIn('id',$oficeId);
            })->get()->pluck('office_name_and_address','id')->toArray();
        }else{
            return $this->model
            ->orderBy('name')
            ->when($permissionAll == false, function ($query) use($oficeId){
                 $query->whereIn('id',$oficeId);
            })->get()->pluck('name','id')->toArray();
        }
     }


     public function getPermissionBaseLocationList(){

        $oficeId = null;
        $permissionAll = false;
        if(\Auth::user()->hasPermissionTo('ids_view_allocated_locaion_schedule')){
            $permissionAll = false;
            $oficeId = $this->idsLocationAllocationRepository->getByUserLocations(\Auth::id());
        }

        if(\Auth::user()->hasPermissionTo('ids_view_all_schedule')){
            $permissionAll = true;
        }

        return $this->model
        ->orderBy('name')
        ->when($permissionAll == false, function ($query) use($oficeId){
                $query->whereIn('id',$oficeId);
        })
        ->select('id','name', 'adress','is_photo_service')
        ->get();

     }

     /**
     * Get single offfice details
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

     /**
     * Store a newly created offfice in storage.
     *
     * @param  $request
     * @return object
     */

    public function store($inputs){
        unset($inputs['intervals']);
        $inputs['is_photo_service'] = isset($inputs['is_photo_service']) ? 1 : 0;
        return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
    }

    /**
     * Get single offfice details
     *
     * @param $id
     * @return object
     */
    public function destroy($id){
        return $this->model->find($id)->delete();
    }


}
