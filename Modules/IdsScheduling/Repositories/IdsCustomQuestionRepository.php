<?php

namespace Modules\IdsScheduling\Repositories;

use Modules\Admin\Models\IdsCustomQuestionAnswer;
use Modules\Admin\Repositories\IdsCustomQuestionRepository as IdsCustomQuestionRepo;
use Modules\Admin\Repositories\IdsCustomQuestionOptionAllocationRepository as IdsCustomQuestionOptionAllocation;

class IdsCustomQuestionRepository
{

    protected $model;
    protected $idsCustomQuestion, $idsCustomQuestionOptionAllocation;
    protected $idsCustomQuestionAnswers;

    /**
     * IdsCustomQuestionRepository constructor.
     * @param IdsCustomQuestion $idsCustomQuestion
     */
    public function __construct(
        IdsCustomQuestionRepo $idsCustomQuestion,
        IdsCustomQuestionOptionAllocation $idsCustomQuestionOptionAllocation,
        IdsCustomQuestionAnswer $idsCustomQuestionAnswers
    )
    {
        $this->idsCustomQuestion = $idsCustomQuestion;
        $this->idsCustomQuestionAnswers = $idsCustomQuestionAnswers;
        $this->idsCustomQuestionOptionAllocation = $idsCustomQuestionOptionAllocation;
    }

    public function getCustomQuestionsWithOptions() {
        $all_questions = $this->idsCustomQuestion->getAllWithOptions();
        $question_arr = array();
        $option_key = 'IdsCustomQuestionAllocation.*.idsCustomOption';
        foreach ($all_questions as $key => $each_question) {
            $question = array();
            $question['id'] = $each_question->id;
            $question['question'] = $each_question->question;
            $question['is_required'] = $each_question->is_required;
            $question['other'] = $each_question->has_other;
            $option_arr = array();
            $option_data_arr = data_get($each_question,$option_key);
            foreach ( $option_data_arr as $each_option){
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

            $data['ids_entry_id'] = $inputs['ids_entry_id'];
            foreach($inputs['question_ids'] as $questionId){
                $data['ids_custom_questions_id'] = $questionId;
                $data['ids_custom_option_id'] = $inputs['selected_option_id_'.$questionId];

                $surveyQuestions = $this->idsCustomQuestion->getActive($questionId);
                $optionAllocation = $this->idsCustomQuestionOptionAllocation->getAllByQuestionAndOption($data);

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
                elseif(!empty($surveyQuestions) && $data['ids_custom_option_id'] != null && empty($optionAllocation)){
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
                    $data['ids_custom_questions_str'] = $surveyQuestions->question;
                    $data['ids_custom_option_str'] = $optionAllocation->idsCustomOption->custom_question_option;
                    $data['other_value'] = $inputs['other_option_vale_'.$questionId];
                    $data['other_value'] = htmlspecialchars($data['other_value']);
                    $this->idsCustomQuestionAnswers->create($data);
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
        return $this->idsCustomQuestionAnswers
        ->where('ids_entry_id',$inputs['old_ids_entry_id'])
        ->update(['ids_entry_id'=>$inputs['ids_entry_id']]);
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
