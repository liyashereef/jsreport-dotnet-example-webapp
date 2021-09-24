<?php

namespace Modules\Hranalytics\Repositories;

use Modules\Hranalytics\Models\CandidateScreeningCompetencyMatrix;

class CandidateScreeningCompetencyMatrixRepository
{

    /**
     * Create a new CapacityTool instance.
     *
     * @param  Modules\Admin\Models\CapacityTool $capacityTool
     */
    public function __construct()
    {
        $this->model = new CandidateScreeningCompetencyMatrix;
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
}
