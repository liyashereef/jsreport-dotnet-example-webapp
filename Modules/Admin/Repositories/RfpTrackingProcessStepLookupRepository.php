<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\RfpProcessStepLookups;

class RfpTrackingProcessStepLookupRepository
{

    protected $model;

    public function __construct(RfpProcessStepLookups $rfpProcessstepLookups)
    {
        $this->model = $rfpProcessstepLookups;
    }

     /**
     *  Function to get all the rfp process Steps
     *
     *  @param empty
     *  @return  array
     *
     */
    public function getAll()
    {
        
        return $this->model->select(['id','step_number', 'process_steps','created_at','updated_at'])->orderBy('step_number','asc')->get();
    }

     /**
     *  Function to save new rfp process Steps
     *
     *  @param empty
     *  @return  array
     *
     */
    public function save($data)
     {
           return $this->model->updateOrCreate(array('id' => $data['id']), $data);        
     }

     /**
     *  Function to edit Single resouce
     *
     *  @param empty
     *  @return  array
     *
     */

     public function get($id)
    {
        return $this->model->find($id);
    }


     /**
     *  Function to delete Single resouce
     *
     *  @param empty
     *  @return  array
     *
     */
     public function delete($id)
    {
        return $this->model->destroy($id);
    }
         }
