<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCandidateScreeningCompetencyMatrix;


class RecCandidateScreeningCompetencyMatrixRepository
{

    /**
     * Create a new CapacityTool instance.
     *
     * @param  Modules\Admin\Models\CapacityTool $capacityTool
     */
    public function __construct()
    {
        $this->model = new RecCandidateScreeningCompetencyMatrix;
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
     * Get all parent questions with answers
     *
     * @param array
     * @return array
     */
    public function store($competencyMatrix)
    {
      
        return $this->model->create($competencyMatrix);

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


    public function deleteAll($candidate_id)
    {
        //return $this->model->destroy($id);
        RecCandidateScreeningCompetencyMatrix::where('candidate_id', $candidate_id)->delete();
    }
}
