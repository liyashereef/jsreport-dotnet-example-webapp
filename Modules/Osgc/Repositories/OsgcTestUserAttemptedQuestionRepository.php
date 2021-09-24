<?php

namespace Modules\Osgc\Repositories;


use Modules\Osgc\Models\TestUserAttemptedQuestion;
use App\Services\HelperService;
use Modules\Osgc\Models\TestCourseQuestionOption;
class OsgcTestUserAttemptedQuestionRepository
{
      /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $helperService;

    /**
     * Create a new TrainingCourseLookupRepository instance.
     *
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(TestUserAttemptedQuestion $attemptedQuestion,
     HelperService $helperService,TestCourseQuestionOption $testCourseQuestionOption)
    {
        $this->model = $attemptedQuestion;
        $this->testCourseQuestionOption = $testCourseQuestionOption;
        $this->helperService = $helperService;
    }

    /**
     * Get all by test_user_result_id
     *
     * @param empty
     * @return object
     */
    public function getQuestionAndOptionByResultId($testUserResultId)
    {
        return $this->model->select(['test_course_question_id','test_course_question_option_id'])
            ->where('test_user_result_id',$testUserResultId)
            ->get();
    }
    public function store($inputs){
        return $this->model->create($inputs);
    }

    /**
     * Update exam question's selected option.
     *
     * @param array[test_user_result_id,question_id,selected_option]
     * @return object
     */

    public function updateExamAnswers($inputs){
       try {
           \DB::beginTransaction();
            
            $data['test_user_result_id'] = $inputs['test_user_result_id'];

            foreach($inputs['test_result_array'] as $value){

                $data['test_course_question_id'] = $value['question_id'];
                $data['selected_option'] = $value['selected_option'];

                $isExists = $this->model
                ->where('test_course_question_id',$value['question_id'])
                ->where('test_course_question_option_id',$value['selected_option'])
                ->where('test_user_result_id',$inputs['test_user_result_id'])
                ->first();

                if(empty($isExists)){
                    $this->updateQuestionAnswers($data);
                }
                
            }
            
            \DB::commit();
            return response()->json(array('success' => true, 'message' => 'Success'));

         } catch (\Exception $e) {dd($e);
                \DB::rollBack();
                return response()->json(array('success' => false,'message' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
            }
        
    }
     /**
     * Update exam question's selected option.
     *
     * @param array[test_user_result_id,question_id,selected_option]
     * @return object
     */

    public function updateQuestionAnswers($inputs){ 
        $questionOptions = $this->testCourseQuestionOption
                            ->where('question_id',$inputs['test_course_question_id'])
                            ->where('id',$inputs['selected_option'])
                            ->first();

        if(!empty($questionOptions)){ 
          return $this->model->where('test_course_question_id',$inputs['test_course_question_id'])
            ->where('test_user_result_id',$inputs['test_user_result_id'])
            ->update([
                'test_course_question_option_id'=>$inputs['selected_option'],
                'is_correct_answer'=>$questionOptions->is_correct_answer,
                ]);
        }
    }
    public function getQuestionAttemptedCount($test_user_result_id){
        return $this->model
        ->where('test_user_result_id',$test_user_result_id)
        ->whereNotNull('test_course_question_option_id')
        ->count();
    }
    public function getRightAnswerCount($test_user_result_id){
        return $this->model
        ->where('test_user_result_id',$test_user_result_id)
        ->where('is_correct_answer',1)
        ->count();
    }
    
}
