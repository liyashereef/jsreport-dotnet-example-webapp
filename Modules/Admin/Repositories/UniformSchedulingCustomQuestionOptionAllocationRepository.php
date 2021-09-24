<?php


namespace Modules\Admin\Repositories;


use Modules\Admin\Models\UniformSchedulingCustomQuestionOptionAllocation;

class UniformSchedulingCustomQuestionOptionAllocationRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new instance.
     *
     * @param UniformSchedulingCustomQuestionOptionAllocation $uniformSchedulingCustomQuestionOptionAllocation
     */
    public function __construct(
        UniformSchedulingCustomQuestionOptionAllocation $uniformSchedulingCustomQuestionOptionAllocation)
    {
        $this->model = $uniformSchedulingCustomQuestionOptionAllocation;
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
            ->orderby('option_sort_order','asc')
            ->with('uniformSchedulingCustomQuestions', 'uniformSchedulingCustomQuestionOption')
            ->get();
    }

    /**
     * Get list by Question,Option
     *
     * @param ids_custom_question_id, ids_custom_option_id
     * @return array
     */
    public function getAllByQuestionAndOption($inputs)
    {
        return $this->model
            ->where('uniform_scheduling_custom_question_id',$inputs['uniform_scheduling_custom_question_id'])
            ->where('uniform_scheduling_custom_option_id',$inputs['uniform_scheduling_custom_option_id'])
            ->with('uniformSchedulingCustomQuestionOption')
            ->first();
    }


    /**
     * Display details of singlegroup
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($question_id,$option_id)
    {
        $data['uniform_scheduling_custom_question_id']=$question_id;
        $data['uniform_scheduling_custom_option_id']=$option_id;
        $data['id']=null;
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getOptionsByQuestion($question_id)
    {
        return $this->model
            ->where('uniform_scheduling_custom_question_id',$question_id)
            ->with('uniformSchedulingCustomQuestionOption')
            ->orderby('option_sort_order')
            ->get();
    }

    public function deleteBasedonQuestionId($question_id)
    {
       return $this->model->where('uniform_scheduling_custom_question_id',$question_id)->delete();
    }




}
