<?php

namespace Modules\LearningAndTraining\Repositories;


use Modules\LearningAndTraining\Models\TestCourseQuestion;
use Modules\LearningAndTraining\Models\TestCourseQuestionOption;

class TrainingTestQuestionsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $testCourseQuestion;

    /**
     * Create a new TrainingCategoryLookupRepository instance.
     *
     * @param  \App\Models\TrainingCategory $trainingCategory
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(TestCourseQuestion $testCourseQuestion,TestCourseQuestionOption $testCourseQuestionOption)
    {

        $this->model = $testCourseQuestion;
        $this->testCourseQuestionOption=$testCourseQuestionOption;
    }

    

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {   
        $data['is_mandatory_display']=isset($data['is_mandatory_display'])?1:0;
        $result= $this->model->updateOrCreate(array('id' => $data['id']), $data);
        return $result->id;
    }

    public function optionSave($id,$data)
    {
        $this->testCourseQuestionOption->where('test_course_question_id',$id)->delete(); 
        $arr['test_course_question_id']=$id;
        $answer_index=$data['is_correct_answer'];
        foreach ($data['answer_option'] as $key => $each_value) {
           $arr['is_correct_answer']=( $answer_index==$key)?1:0;  
           $arr['answer_option']=$each_value;
        $this->testCourseQuestionOption->create($arr);
        }
        $this->model->find($id)->touch();
    
      
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getExamQuestions($id)
    {
        $data=$this->model->where('test_course_master_id',$id)->get();
        return $this->getExamQuestionsArray($data);

    }

     /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getExamQuestionsArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_data) {
            $each_row["updated_at"] = $each_data->updated_at->format('Y-m-d h:i:s');
            $each_row["id"] = $each_data->id;
            $each_row["test_question"] = $each_data->test_question;
            $each_row["is_mandatory_display"] = ($each_data->is_mandatory_display==1)?'Yes':'No';
            array_push($datatable_rows, $each_row);

        }
        return $datatable_rows;

    }
    

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function getQuestion($id)
    {
        return $this->model->with('test_question_options')->find($id);
    }
    
    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function destroyQuestion($id)
    {
        return $this->model->destroy($id);
    }

    public function deleteQuestions($setting_id)
    {
        return $this->model->where('test_course_master_id',$setting_id)->delete();
    }
     

     public function questionDisplay($data)
     {
       return $this->model->where('test_course_master_id',$data['id'])->with('test_question_options')
       ->orderBy('is_mandatory_display','desc')
       ->when($data['random_question'], function($q) use($data){
            return $q->inRandomOrder();
        })
        ->when($data['number_of_question'], function($q) use($data){
            return $q->limit($data['number_of_question']);
        })
        ->get();
     }

    /**
     * Display all questions including trashed
     *
     * @param $ids
     * @return object
     */

     public function getQuestionByIds($ids){
        return $this->model->whereIn('id',$ids)
        ->with('test_question_options')
        ->orderBy('is_mandatory_display','desc')
        ->withTrashed()
        ->get();
     }
    
}
