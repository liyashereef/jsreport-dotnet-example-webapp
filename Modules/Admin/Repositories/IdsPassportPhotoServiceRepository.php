<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IdsPassportPhotoService;

class IdsPassportPhotoServiceRepository
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
    public function __construct(IdsPassportPhotoService $idsPassportPhotoService)
    {
        $this->model = $idsPassportPhotoService;
    }

     /**
     * Get list
     *
     * @param empty
     * @return array
     */

    public function getAll(){
        return $this->model->orderby('id','desc')->get();
     }
     public function all(){
        return $this->model->all();
     }

     public function allArray(){
        return $this->model->all()->pluck('name_rate','id');
     }

      /**
      * Get single details
      *
      * @param $id
      * @return object
      */
     public function getById($id)
     {
         return $this->model->find($id);
     }

      /**
      * Store a newly created in storage.
      *
      * @param  $request
      * @return object
      */

     public function store($inputs){
         return $this->model->updateOrCreate(['id' => $inputs['id']], $inputs);
     }

     /**
      * Get single details
      *
      * @param $id
      * @return object
      */
     public function destroy($id){
         return $this->model->find($id)->delete();
     }


}
