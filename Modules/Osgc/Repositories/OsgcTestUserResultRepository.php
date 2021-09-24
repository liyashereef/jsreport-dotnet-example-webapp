<?php

namespace Modules\Osgc\Repositories;


use Modules\Osgc\Models\TestUserResult;
use Modules\Osgc\Models\TestCourseMaster;
use Modules\Osgc\Repositories\OsgcTestUserAttemptedQuestionRepository;
use App\Services\HelperService;
class OsgcTestUserResultRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $helperService;
     /**
     * Create a new OsgcCourseLookupRepository instance.
     *
     * @param  \App\Models\OsgcCourse $trainingCourse
     */
    public function __construct(TestUserResult $model,
    HelperService $helperService,OsgcTestUserAttemptedQuestionRepository $testUserAttemptRepo,
    TestCourseMaster $testCourseMaster)
    {

        $this->model = $model;
        $this->helperService = $helperService;
        $this->testCourseMaster = $testCourseMaster;
        $this->testUserAttemptRepo = $testUserAttemptRepo;
    }

    /**
     * Get all attempt count by user_id
     *
     * @param empty
     * @return count
     */
    public function getTestAttemptByUserId($userId,$sectionId)
    {
        return $this->model
            ->where('user_id',$userId)
            ->where('status',1)
            ->where('course_section_id',$sectionId)
            ->first();
    }


    /**
     * Get data by id
     *
     * @param id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }
     /**
     * User exam draft availability checking.
     * Exam set has only 1 day validity. After expiry status will change to close(3) 
     *
     * @param $inputs['test_course_master_id'];
     * @return exam_id, message
     */

    public function isDraftExists($inputs){
       
        return $isExists = $this->model
                ->where('test_course_master_id',$inputs['test_course_master_id'])
                ->where('user_id',\Auth::guard('osgcuser')->user()->id)
                ->orderBy('id','DESC')
                ->first();

        
    }
  

     /**
     * Fetch all data based on Filters.
     *  
     * @param $inputs['test_course_master_id','status','ids','user_id','last_one'];
     *  
     * @return 
     */

    public function getAllBasedOnFilters($inputs){ 
        $query = $this->model;

        $query=$query->when(isset($inputs['test_course_master_id']), function($query) use($inputs){
             $query->where('test_course_master_id',$inputs['test_course_master_id']);
        });

        $query=$query->when(isset($inputs['status']), function($query) use($inputs){
             $query->where('status',$inputs['status']);
        });

        $query=$query->when(isset($inputs['ids']), function($query) use($inputs){ 
            if(is_array($inputs['ids'])){
                 $query->whereIn('id',$inputs['ids']);
            }else{ 
                 $query->where('id',$inputs['ids']);
            }
        });

         $query=$query->when(isset($inputs['user_id']), function($query) use($inputs){
             $query->where('user_id',$inputs['user_id']);
        });

        $query=$query->when(isset($inputs['course_section_id']), function($query) use($inputs){
             $query->where('course_section_id',$inputs['training_course_id']);
        });

        $query=$query->when(isset($inputs['is_exam_pass']), function($query) use($inputs){
             $query->where('is_exam_pass',$inputs['is_exam_pass']);
        });
        
        $query=$query->when(isset($inputs['status']), function($query) use($inputs){
             $query->where('status',$inputs['status']);
        });
        
        if(!empty($inputs) && isset($inputs['last_one']) && $inputs['last_one'] == true){
           return $query->orderBy('id','DESC')->first();
        }else{
            return $query->get(); 
        }
        
    }

         /**
     * When question listing store user exam details and questions 
     *
     * @param $inputs['test_course_master_id'] , $inputs['test_course_question_ids'] = array;
     * @return 
     */

    public function storeTestAttempt($inputs){ 
        $test_user_result_id = '';
        try {
            \DB::beginTransaction();
                $userResult = [];
                $courseQuestion = [];
                if(!empty($inputs)){

                    $testCourseMaster = $this->testCourseMaster->find($inputs['test_course_master_id']); 
                    
                    if(!empty($testCourseMaster)){
                        
                       
                        $userResult['test_course_master_id'] = $inputs['test_course_master_id'];
                        $userResult['course_pass_percentage'] = $testCourseMaster->pass_percentage;
                        $userResult['user_id'] = \Auth::guard('osgcuser')->user()->id;
                        $userResult['course_section_id'] = $testCourseMaster->osgc_course_section_id;
                        // $userResult['total_questions'] = $testCourseMaster->number_of_question;
                        $userResult['total_questions'] = sizeof($inputs['test_course_question_ids']);
                        
                        $result = $this->model->create($userResult);
                        
                        if(!empty($result)){
                            $test_user_result_id = $result->id;
                            $courseQuestion['test_user_result_id'] = $result->id;
                            
                            foreach($inputs['test_course_question_ids'] as $value){
                                $courseQuestion['test_course_question_id'] =  $value;
                                $this->testUserAttemptRepo->store($courseQuestion);
                            }
                        }
                        $message = 'Exam attempt successfully created'; 
                    }else{
                        $message = 'Course master not found';   
                    }
                }else{
                    $message = 'Params not found';
                }
            \DB::commit();
            return response()->json(array('success' => true, 'message' => $message,'test_user_result_id'=>$test_user_result_id));
        } catch (\Exception $e) {//dd($e);
            \DB::rollBack();
            return response()->json(array('success' => false,'test_user_result_id'=>$test_user_result_id, 'message' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    public function submitExam($id,$inputs){
        return $this->model->where('id',$id)->update($inputs);
    }
}
