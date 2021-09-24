<?php


namespace Modules\Admin\Repositories;


use Modules\Admin\Models\IdsCustomQuestionOptionAllocation;

class IdsCustomQuestionOptionAllocationRepository
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
     * @param IdsCustomQuestionOptionAllocation $idsCustomQuestionOptionAllocation
     */
    public function __construct(
        IdsCustomQuestionOptionAllocation $idsCustomQuestionOptionAllocation)
    {
        $this->model = $idsCustomQuestionOptionAllocation;
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
            ->orderby('Ids_option_sort_order','asc')
            ->with('idsCustomQuestions', 'idsCustomOption')
            ->get();
    }

    /**
     * Get list by Question,Option
     *
     * @param ids_custom_questions_id, ids_custom_option_id
     * @return array
     */
    public function getAllByQuestionAndOption($inputs)
    {
        return $this->model
            ->where('ids_custom_questions_id',$inputs['ids_custom_questions_id'])
            ->where('ids_custom_option_id',$inputs['ids_custom_option_id'])
            ->with('idsCustomOption')
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
        $data['ids_custom_questions_id']=$question_id;
        $data['ids_custom_option_id']=$option_id;
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
            ->where('ids_custom_questions_id',$question_id)
            ->with('idsCustomOption')
            ->orderby('Ids_option_sort_order')
            ->get();
    }

    public function deleteBasedonQuestionId($question_id)
    {
       return $this->model->where('ids_custom_questions_id',$question_id)->delete();
    }




}
