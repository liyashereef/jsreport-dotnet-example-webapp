<?php

namespace Modules\LearningAndTraining\Repositories;

use Modules\LearningAndTraining\Models\TestCourseMaster;
use Modules\LearningAndTraining\Models\TestCourseQuestion;
use Modules\LearningAndTraining\Repositories\TrainingTestQuestionsRepository;

class TrainingTestSettingsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $trainingCourseModel;

    /**
     * Create a new TrainingCategoryLookupRepository instance.
     *
     * @param  \App\Models\TrainingCategory $trainingCategory
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(TestCourseMaster $testCourseMaster,TestCourseQuestion $testCourseQuestion,TrainingTestQuestionsRepository $trainingTestQuestionsRepository)
    {
        $this->model = $testCourseMaster;
        $this->testCourseQuestion = $testCourseQuestion;
        $this->trainingTestQuestionsRepository=$trainingTestQuestionsRepository;
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('course_category', 'asc')->select(['id', 'course_category', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('course_category', 'asc')->pluck('course_category', 'id')->toArray();
    }

    

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $data['number_of_question']= isset($data['number_of_question'])?$data['number_of_question']:0;
        $data['active']= isset($data['active'])?$data['active']:0;
        $data['pass_percentage']= round($data['pass_percentage']);
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function updateStatus($data)
    {
        if($data['active']==1)
        {
        $this->model->where('id','!=',$data['id'])->where('training_course_id',$data['training_course_id'])->update(['active'=>0]);   
        }
        return;
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getExamSettings($id)
    {
        $data=$this->model->where('training_course_id',$id)->withCount('test_questions')->get();
        return $this->getExamSettingArray($data);

    }

    public function getExamSettingArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            $each_row["exam_name"] = $each_record->exam_name;
            $each_row["id"] = $each_record->id;
            $each_row["number_of_question"] = ($each_record->number_of_question==0)?$each_record->test_questions_count:$each_record->number_of_question;
            $each_row["pass_percentage"] = round($each_record->pass_percentage);
            $each_row["active"]=($each_record->active==0)?'Inactive':'Active'; 
            $each_row["updated_at"] = $each_record->updated_at->format('Y-m-d h:i:s');
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    
    public function getActiveSettingByCourse($courseId){
        return $this->model->where('training_course_id',$courseId)->where('active',1)->first();
    }
    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getActiveExamSettings($id)
    {

        $testSettings=$this->model->where('training_course_id',$id)->where('active',1)->first();
        return $this->trainingTestQuestionsRepository->questionDisplay($testSettings);
        

    }


    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getExamQuestions($id)
    {
          return $this->testCourseQuestion->where('test_course_master_id',$id)->get();

    }
    

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function getSettings($id)
    {
        return $this->model->withCount('test_questions')->find($id);
    }
    

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function destroySetting($id)
    {
        return $this->model->destroy($id);
    }


    public function hasActiveTest($id)
    {
        return $this->model->where('training_course_id',$id)->whereHas('test_questions')->where('active',1)->count(); 
    }

     public function getActiveTest($id)
    {
        return $this->model->where('training_course_id',$id)->where('active',1)->first(); 
    }

    
}