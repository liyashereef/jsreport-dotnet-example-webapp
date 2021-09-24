<?php

namespace Modules\Hranalytics\Repositories;

use Modules\Hranalytics\Models\CandidateScreeningPersonalityTestQuestion;

class CandidateScreeningPersonalityTestQuestionRepository
{

    /**
     * Create a new CandidateScreeningPersonalityTestQuestion instance.
     *
     * 
     */
    public function __construct()
    {
        $this->model = new CandidateScreeningPersonalityTestQuestion;
    }

    /**
     * Get all question list
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
     * @param empty
     * @return array
     */
    public function getAllQuestionWithOptions()
    {
        return $this->model->with(['options'])->get();
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
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($data)
    {
        //return $this->model->updateOrCreate(array('id' => $data['id']), $data);
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
