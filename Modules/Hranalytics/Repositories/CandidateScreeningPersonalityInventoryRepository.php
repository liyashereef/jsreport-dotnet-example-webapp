<?php

namespace Modules\Hranalytics\Repositories;

use Modules\Hranalytics\Models\CandidateScreeningPersonalityInventory;

class CandidateScreeningPersonalityInventoryRepository
{

    /**
     * Create a new CapacityTool instance.
     *
     * @param  Modules\Admin\Models\CapacityTool $capacityTool
     */
    public function __construct()
    {
        $this->model = new CandidateScreeningPersonalityInventory;
    }

    /**
     * Get all resource list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->get();
    }

     /**
     * Get all resource based on candidate
     *
     * @param integer candidate_id
     * @return array
     */
    public function getCandidateList($candidate_id)
    {
        return $this->model->where('candidate_id',$candidate_id)->get();
    }

    /**
     * Get sum of a column
     *
     * @param   
     *    integer candidate_id
     *    integer column
     *    string option   (a or b)
     *       
     * @return integer
     */
    public function calculateColumnSum($candidate_id,$column,$option)
    {
        $query =  $this->model->where('candidate_id',$candidate_id)
                               ->whereHas('question',function($query)use($column){
                                   $query->where('column',$column);
                               }) 
                               ->whereHas('answer',function($query)use($option){
                                    $query->where('option',$option);
                               });
                            
         return $query->count();
    }

    /**
     * Get all parent questions with answers
     *
     * @param array
     * @return array
     */
    public function store($inventory)
    {
        return $this->model->create($inventory);

    }


    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        //return $this->model->find($id);
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        //return $this->model->destroy($id);
    }
}
