<?php

namespace Modules\Recruitment\Repositories;

use Modules\Recruitment\Models\RecCandidateScreeningPersonalitySum;

class RecCandidateScreeningPersonalitySumRepository
{

    /**
     * Create a new CandidateScreeningPersonalitySumRepository instance.
     *
     *
     */
    public function __construct()
    {
        $this->model = new RecCandidateScreeningPersonalitySum;
    }

    /**
     * Get all sum of a candidate
     *
     * @param integer candidate_id
     * @return array
     */
    public function getAll($candidate_id)
    {
        return $this->model->where('candidate_id', $candidate_id)->get();
    }


    /**
     * Display details of single resource
     *
     * @param $id
     * @return integer
     */
    public function get($candidate_id, $column, $option)
    {
        return $this->model->where('candidate_id', $candidate_id)
            ->where('column', $column)
            ->where('option', $option)
            ->pluck('sum');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function store($data)
    {
        return $this->model->updateOrCreate($data);
    }
}
