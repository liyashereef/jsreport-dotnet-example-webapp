<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsPaymentReasons;

class IdsPaymentReasonsRepository
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
    public function __construct(IdsPaymentReasons $idsPaymentReasons)
    {
        $this->model = $idsPaymentReasons;
    }

    /**
     * Get offfice list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
       return $this->model->orderby('name')->get();
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


}
