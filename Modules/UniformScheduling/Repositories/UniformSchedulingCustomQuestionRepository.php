<?php

namespace Modules\UniformScheduling\Repositories;

use Modules\Admin\Models\UniformSchedulingCustomQuestionAnswer;
use Modules\Admin\Repositories\UniformSchedulingCustomQuestionRepository as customQuestionRepo;
use Modules\Admin\Repositories\UniformSchedulingCustomQuestionOptionAllocationRepository as customQuestionOptionAllocation;

class UniformSchedulingCustomQuestionRepository
{

    protected $model;
    protected $customQuestion, $customQuestionOptionAllocation;
    protected $customQuestionAnswers;

    /**
     * IdsCustomQuestionRepository constructor.
     * @param IdsCustomQuestion $idsCustomQuestion
     */
    public function __construct(
        customQuestionRepo $customQuestion,
        customQuestionOptionAllocation $customQuestionOptionAllocation,
        UniformSchedulingCustomQuestionAnswer $customQuestionAnswer
    )
    {
        $this->customQuestion = $customQuestion;
        $this->customQuestionAnswer = $customQuestionAnswer;
        $this->customQuestionOptionAllocation = $customQuestionOptionAllocation;
    }

    public function getCustomQuestionsWithOptions() {
        $all_questions = $this->customQuestion->getAllWithOptions();
        $question_arr = array();
        $option_key = 'uniformSchedulingCustomQuestionOptionAllocation.*.uniformSchedulingCustomQuestionOption';
        // uniformSchedulingCustomQuestionOptionAllocation.uniformSchedulingCustomQuestionOption
        foreach ($all_questions as $key => $each_question) {
            // dd($each_question);
            $question = array();
            $question['id'] = $each_question->id;
            $question['question'] = $each_question->question;
            $question['is_required'] = $each_question->is_required;
            $question['other'] = $each_question->has_other;
            $option_arr = array();
            $option_data_arr = data_get($each_question,$option_key);
            foreach ($option_data_arr as $each_option){
                if(isset($each_option)){
                    $option = array();
                    $option['id'] = $each_option->id;
                    $option['custom_question_option'] = $each_option->custom_question_option;
                    array_push($option_arr,$option);
                }
            }
            $question['options'] = $option_arr;
            array_push($question_arr,$question);
        }
        return $question_arr;
    }

    public function saveAnswers($inputs) {

        try{

            $data['uniform_scheduling_entry_id'] = $inputs['uniform_scheduling_entry_id'];
            foreach($inputs['question_ids'] as $questionId){

                $data['uniform_scheduling_custom_question_id'] = $questionId;
                $data['uniform_scheduling_custom_option_id'] = $inputs['selected_option_id_'.$questionId];

                $surveyQuestions = $this->customQuestion->getActive($questionId);
                $optionAllocation = $this->customQuestionOptionAllocation->getAllByQuestionAndOption($data);

                $returnError = false;
                if(empty($surveyQuestions)){
                    /** Checking question is empty */
                    $returnError = true;
                    $return = ['success' => false,
                    'reload'=>true,
                    'modalHide' => true,
                    'showMessage'=>true,
                    'message'=>'Something went wrong.Reload and try again'];
                }
                elseif(!empty($surveyQuestions) && $surveyQuestions->is_required == 1 && empty($optionAllocation)){
                  /** Checking question is not empty.
                     * Option allocation is empty.
                     * Question is is_required.
                     * */
                    $returnError = true;
                    $return = ['success' => false,
                    'reload'=>false,
                    'modalHide' => false,
                    'showMessage'=>true,
                    'message'=>'Please fill all mandatory questions'];
                }
                elseif(!empty($surveyQuestions) && $data['uniform_scheduling_custom_option_id'] != null && empty($optionAllocation)){
                    /** Checking question is not empty
                     * Given answer is not null.
                     * Option allocation is empty
                     * */
                    $returnError = true;
                    $return = ['success' => false,
                    'reload'=>true,
                    'modalHide' => true,
                    'showMessage'=>true,
                    'message'=>'Something went wrong.Reload and try again'];
                }

                if($returnError){
                    return $return;
                }
                if(!empty($surveyQuestions) && !empty($optionAllocation)){
                    $data['custom_questions_str'] = $surveyQuestions->question;
                    $data['custom_option_str'] = $optionAllocation->uniformSchedulingCustomQuestionOption->custom_question_option;
                    $data['other_value'] = $inputs['other_option_vale_'.$questionId];
                    $data['other_value'] = htmlspecialchars($data['other_value']);
                    $this->customQuestionAnswer->create($data);
                }

            }

            $return = ['success' => true,'message'=>'Success','showMessage'=>false];
            return $return;

        } catch (\Exception $e) {
            $return = ['success' => false,'message'=>$e->getMessage(),'showMessage'=>false];
            return $return;
        }
    }

    public function resheduleEntry($inputs){
        return $this->customQuestionAnswer
        ->where('uniform_scheduling_entry_id',$inputs['old_entry_id'])
        ->update(['uniform_scheduling_entry_id'=>$inputs['new_entry_id']]);
    }

    /**
     * Get list
     *
     * @param empty
     * @return array
     */
    public function getAllAnaswerd(){
        return $this->idsCustomQuestion->getAllAnaswerd();
    }

}
