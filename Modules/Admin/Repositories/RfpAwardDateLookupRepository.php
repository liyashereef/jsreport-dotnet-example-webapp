<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\RfpAwardDateLookups;

class RfpAwardDateLookupRepository
{

    protected $model;

    public function __construct(RfpAwardDateLookups $rfpAwarddateLookups)
    {
        $this->model = $rfpAwarddateLookups;
    }


     /**
     *  Function to get  rfp Award Date
     *
     *  @param empty
     *  @return  array
     *
     */

    public function getAll()
    {
        
        return $this->model->select(['id','award_dates','created_at','updated_at'])->orderBy('award_dates','asc')->get();
    }



     /**
     *  Function to save  rfp Award Date
     *
     *  @param empty
     *  @return  array
     *
     */
    public function save($data)
     {
         
         return $this->model->where('id', $data['id'])->update(['award_dates' => $data['award_dates']]);      
     }



     /**
     *  Function to edit  rfp Award Date
     *
     *  @param empty
     *  @return  array
     *
     */
     public function get($id)
     {
         return $this->model->find($id);
     }

   

}