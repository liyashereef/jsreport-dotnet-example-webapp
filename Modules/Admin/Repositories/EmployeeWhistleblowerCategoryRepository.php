<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\EmployeeWhistleblowerCategories;

class EmployeeWhistleblowerCategoryRepository{

    protected $model;

    public function __construct(EmployeeWhistleblowerCategories $model)
    {
       $this->model = $model;
    }

    public function getAll(){

        return $this->model->select(['id','roles','shortname','created_at','updated_at'])->get();

     }
     public function getList()
    {
        return $this->model->orderBy('roles', 'asc')->pluck('roles', 'id')->toArray();
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  $data
     * @return object
     */

    public function save($data)
    {
       $result =  $this->model->updateOrCreate(array('id' => $data['id']), $data);
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
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
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

    public function getCategoriesForApp()
    {
        return $this->model->select(['id', 'roles'])->orderBy('roles', 'asc')->get();
    }

}
