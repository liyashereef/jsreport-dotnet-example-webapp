<?php

namespace Modules\Admin\Repositories;

use Modules\IdsScheduling\Models\IdsPaymentMethods;

class IdsPaymentMethodsRepository
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
    public function __construct(IdsPaymentMethods $idsPaymentMethods)
    {
        $this->model = $idsPaymentMethods;
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
       return $this->model->orderby('id','desc')->get();
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
    /**
     * Get deatails By short name
     *
     * @param $id
     * @return object
     */
    public function getByShortName($shortName)
    {
        return $this->model->where('short_name',$shortName)->first();
    }

    public function getPaymentMethodsInArray(){
        return $this->model->where('active',true)
        ->where('short_name','!=','STRIPE')
        ->pluck('full_name','id')
        ->toArray();
    }
}
