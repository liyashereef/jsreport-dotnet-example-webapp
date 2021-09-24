<?php

namespace Modules\CapacityTool\Repositories;

use Modules\CapacityTool\Models\CapacityToolQuestion;
use Illuminate\Support\Arr;

class CapacityToolQuestionRepository
{

    /**
     * Create a new CapacityTool instance.
     *
     * @param  Modules\Admin\Models\CapacityTool $capacityTool
     */
    public function __construct()
    {
        $this->model = new CapacityToolQuestion;
    }

    /**
     * Get all resource list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderBy('order')->get();
    }

    /**
     * Get all parent questions with answers
     *
     * @param empty
     * @return array
     */
    public function getAllParentQuestions()
    {
        $parentQuestions = $this->model->where('is_parent', 0)->orderBy('order')->get();
        $parentQuestionsWithAnswers = $this->getQuestionsWithAnswers($parentQuestions);
        return $parentQuestionsWithAnswers;
    }

    /**
     * Get sub questions with answers
     *
     * @param question_id integer, answer_id integer
     * @return collection
     */
    public function getSubQuestions($question_id, $answer_id)
    {
        $subQuestions = $this->model->where('is_parent', 1)->orderBy('order')->where('parent_id', $question_id)->where('show_child_value', $answer_id)->get();
        $subQuestionsWithAnswers = $this->getQuestionsWithAnswers($subQuestions);
        return $subQuestionsWithAnswers;
    }

    /**
     * Get all questions with answers
     *
     * @param collection
     * @return collection
     */
    public function getQuestionsWithAnswers($questions)
    {
        $questionsWithAnswers = $questions->map(function ($item) {
            if ($item->answer_type != null) {
                $className = $item->answer_type;

                if($className == 'Modules\Admin\Models\Customer')
                {
                    $item->answers = $className::select('project_number','id','client_name')->orderBy('project_number','asc')->get()->toArray();
                    //$item->answers = $className::pluck('project_number','id','client_name')->toArray();
               
                }
                else if($className == 'Modules\Admin\Models\CapacityToolTaskFrequencyLookup')
                {
                    $item->answers = $className::orderBy('sequence_number')->pluck('value','sequence_number','id')->toArray();
                   
                   
                }
                else {
                    $item->answers = $className::orderBy('value','asc')->pluck('value', 'id')->toArray();                                        
                }
                
            }
           
            return $item;
        });
        
        return $questionsWithAnswers;
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
