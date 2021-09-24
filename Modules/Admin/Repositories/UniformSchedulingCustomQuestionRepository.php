<?php

namespace Modules\Admin\Repositories;

use  Modules\Admin\Models\UniformSchedulingCustomQuestion;
use  Modules\Admin\Repositories\UniformSchedulingCustomQuestionOptionRepository;
use  Modules\Admin\Repositories\UniformSchedulingCustomQuestionOptionAllocationRepository;

class UniformSchedulingCustomQuestionRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new UniformSchedulingCustomQuestionRepository instance.
     *
     * @param  Modules\Admin\Models\UniformSchedulingCustomQuestion $uniformSchedulingCustomQuestion
     */
    public function __construct(UniformSchedulingCustomQuestion $uniformSchedulingCustomQuestion,
    UniformSchedulingCustomQuestionOptionRepository $uniformSchedulingCustomQuestionOptionRepository,
    UniformSchedulingCustomQuestionOptionAllocationRepository $uniformSchedulingCustomQuestionOptionAllocationRepository)
    {
        $this->model = $uniformSchedulingCustomQuestion;
        $this->uniformSchedulingCustomQuestionOptionRepository = $uniformSchedulingCustomQuestionOptionRepository;
        $this->uniformSchedulingCustomQuestionOptionAllocationRepository = $uniformSchedulingCustomQuestionOptionAllocationRepository;
    }


    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getDataList()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->with('uniformSchedulingCustomQuestionOptionAllocation.uniformSchedulingCustomQuestionOption')
            ->get();
    }
    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->pluck('question', 'id')->where("is_active", true)
            ->toArray();
    }

    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->where("is_active", true)
            ->get();
    }

    public function getAllWithOptions()
    {
        return $this->model
            ->with('uniformSchedulingCustomQuestionOptionAllocation.uniformSchedulingCustomQuestionOption')
            ->has('uniformSchedulingCustomQuestionOptionAllocation.uniformSchedulingCustomQuestionOption')
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            ->where("is_active", true)
            ->get();
    }

    /**
     * Display details of single
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('uniformSchedulingCustomQuestionOptionAllocation.uniformSchedulingCustomQuestionOption')
            ->with(array('uniformSchedulingCustomQuestionOptionAllocation' => function ($query) {
                $query->where('uniform_scheduling_custom_option_id', '!=', 1);
            }))->find($id);
    }

    /**
     * Display details of active single
     *
     * @param $id
     * @return object
     */
    public function getActive($id)
    {
        return $this->model->where("is_active", true)->find($id);
    }

    /**
     * Store a newly created in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {

        $data['has_other'] = isset($data['has_other']) ? 1 : 0;
        $data['is_required'] = isset($data['is_required']) ? 1 : 0;
        $data['is_active'] = isset($data['is_active']) ? 1 : 0;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteQuestion($id)
    {
        return $this->model->destroy($id);
    }

    public function deleteRelatedRecords($id)
    {
        $details = $this->model->with('uniformSchedulingCustomQuestionOptionAllocation')->find($id);
        // $allocation_details = data_get($details, 'uniformSchedulingCustomQuestionOptionAllocation.*');
        if($details->uniformSchedulingCustomQuestionOptionAllocation){
            foreach ($details->uniformSchedulingCustomQuestionOptionAllocation as $key => $each_allocation) {
                if ($each_allocation->uniform_scheduling_custom_option_id != \Config::get('globals.idsCustomQuestionOther')) {
                    $this->uniformSchedulingCustomQuestionOptionRepository->delete($each_allocation->uniform_scheduling_custom_option_id);
                }
                $this->uniformSchedulingCustomQuestionOptionAllocationRepository->delete($each_allocation->id);
            }
        }

        return true;
    }

    public function getAllAnaswerd()
    {
        return $this->model
            ->orderby('display_order', 'asc')
            ->orderby('question', 'asc')
            // ->where("is_active",true)
            // ->where("id", 10)
            ->whereHas('uniformSchedulingCustomQuestionAnswer')
            ->get();
    }
}

