<?php

namespace Modules\Osgc\Repositories;

use Modules\Osgc\Models\TestCourseMaster;
use Modules\Osgc\Models\TestCourseQuestion;
use Modules\Osgc\Repositories\OsgcTestQuestionsRepository;
use Auth;
class OsgcTestSettingsRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Repository instance.
     *
     *
     * @param  \App\Models\Osgc Course
     */
    public function __construct(TestCourseMaster $testCourseMaster,TestCourseQuestion $testCourseQuestion,OsgcTestQuestionsRepository $osgcTestQuestionsRepository)
    {
        $this->model = $testCourseMaster;
        $this->testCourseQuestion = $testCourseQuestion;
        $this->osgcTestQuestionsRepository=$osgcTestQuestionsRepository;
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('exam_name', 'asc')->select(['id', 'exam_name', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('exam_name', 'asc')->pluck('exam_name', 'id')->toArray();
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
        if (empty($data['id'])) {
            $data['created_by'] = Auth::user()->id;
        }
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getExamSettings($courseId)
    {
        $data=$this->model->with(['course_section','course_section.courseHeading'])->where('course_id',$courseId)->get();
        return $this->getExamSettingArray($data);

    }

    public function getExamSettingArray($data)
    {
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {   
            $each_row["exam_name"] = $each_record->exam_name;
            $each_row["section_name"] = $each_record->course_section->name ?? '';
            $each_row["header_name"] = $each_record->course_section->courseHeading->name ?? '';
            $each_row["id"] = $each_record->id;
            $each_row["number_of_question"] = ($each_record->number_of_question==0)?$each_record->test_questions_count:$each_record->number_of_question;
            $each_row["pass_percentage"] = round($each_record->pass_percentage);
            $each_row["active"]=($each_record->active==0)?'Inactive':'Active'; 
            $each_row["updated_at"] = \Carbon::parse($each_record->updated_at)->format('Y-m-d h:i:s');
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    
    public function getActiveSettingByCourse($courseId){
        return $this->model->where('osgc_course_section_id',$courseId)->where('active',1)->first();
    }
    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function getActiveExamSettings($id)
    {

        $examSetting=$this->model->where('osgc_course_section_id',$id)->where('active',1)->first();
        if($examSetting)
        {
            $questions=$this->osgcTestQuestionsRepository->questionDisplay($examSetting);
            $examSetting['questions']=$questions;
        }
        
        return $examSetting;
        

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
        return $this->model->with('course_section')->withCount('test_questions')->find($id);
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
        return $this->model->where('osgc_course_section_id',$id)->where('active',1)->first(); 
    }
    
    
}
