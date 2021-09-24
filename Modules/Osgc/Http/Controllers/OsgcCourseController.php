<?php

namespace Modules\Osgc\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Osgc\Repositories\OsgcCourseRepository;
use Modules\Osgc\Repositories\OsgcCourseLookupRepository;
use Modules\Osgc\Repositories\OsgcTestQuestionsRepository;
use Modules\Osgc\Repositories\OsgcTestSettingsRepository;
use Modules\Osgc\Repositories\OsgcTestUserResultRepository;
use Modules\Osgc\Repositories\OsgcTestUserAttemptedQuestionRepository;
use Modules\Osgc\Repositories\OsgcUserCourseCompletionRepository;
use Modules\Osgc\Repositories\OsgcUserRepository;
use Modules\Osgc\Models\UserCourseCompletion;
class OsgcCourseController extends Controller
{
    protected $osgcCourseRepository;

    public function __construct(OsgcCourseRepository $osgcCourseRepository,
    OsgcCourseLookupRepository $osgcCourseLookupRepository,
    OsgcTestQuestionsRepository $osgcTestQuestionsRepository,
    OsgcTestSettingsRepository $osgcTestSettingsRepository,
    OsgcTestUserResultRepository $osgcTestUserResultRepository,
    OsgcTestUserAttemptedQuestionRepository $osgcTestUserAttemptedQuestionRepository,
    OsgcUserCourseCompletionRepository $osgcUserCourseCompletionRepository,
    OsgcUserRepository $osgcUserRepository
    ){
        $this->osgcCourseRepository = $osgcCourseRepository;
        $this->osgcCourseLookupRepository = $osgcCourseLookupRepository;
        $this->osgcTestQuestionsRepository = $osgcTestQuestionsRepository;
        $this->osgcTestSettingsRepository = $osgcTestSettingsRepository;
        $this->osgcTestUserAttemptedQuestionRepository = $osgcTestUserAttemptedQuestionRepository;
        $this->osgcTestUserResultRepository = $osgcTestUserResultRepository;
        $this->osgcUserCourseCompletionRepository = $osgcUserCourseCompletionRepository;
        $this->osgcUserRepository = $osgcUserRepository;
        
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function courseDetails($id)
    {
        $firstSection=$isExist='';
        $result=$this->osgcCourseRepository->getCourseDetails($id);//dd($result);
        $isheaderComplted=$this->osgcCourseRepository->isheaderComplted($result);//dd($isheaderComplted);
        if($result)
        {   $headerCount=count($result->ActiveCourseHeaders);
            switch ($headerCount) {
                case "2":
                  $trimValue=70;
                  break;
                case "3":
                    $trimValue=45;
                    break;
                case "4":
                    $trimValue=30;
                    break;
                case "5":
                    $trimValue=23;
                    break;
                case "6":
                    $trimValue=15;
                    break;
                default:
                    $trimValue='';
                    break;
              }
            //dd($trimValue);
            $firstSection=$this->osgcCourseRepository->getFirstSectionId($id);//dd($firstSection);
            $isExist=$this->osgcCourseRepository->getCourseCertificationByUser($id);
            return view('osgc::course.courseDetails',compact('trimValue','result','id','firstSection','isExist','isheaderComplted'));
            
        }else{
            return redirect('osgc/home');
        }
        
    }
    /**
     * Display course content
     *
     * @param $id
     * @return json
     */
    public function showCourseContent(Request $request)
    {
       $sectionDet=array();
       $checkCourseCompletion=$this->osgcCourseRepository->checkCourseCompletion($request);//dd($checkCourseCompletion);
       if($checkCourseCompletion ==true){
            $sectionDet=$this->osgcCourseRepository->getCourseSectionDetails($request->section_id);
            $getCourseId=$this->osgcCourseLookupRepository->getCourseContent($request->section_id);
            $userArr=array('course_id'=>$getCourseId->course_id,'course_section_id'=>$request->section_id);
            $this->osgcUserRepository->updateUserCourseStatus($userArr);
            //$checkAlreadyExist=$this->osgcUserCourseCompletionRepository->getAlreadyExist($request->section_id);
            $checkRowExist=UserCourseCompletion::where('course_section_id',$request->section_id)->where('user_id',\Auth::guard('osgcuser')->user()->id)->first();
            if(empty($checkRowExist))
            {
                $this->osgcUserCourseCompletionRepository->saveUserCourseCompletionStatus($request->section_id);
            } 
            $content=$sectionDet->courseContent->content ?? '';
            $sectionId=$sectionDet->id;
            $path="osgc/video/".$content;
            $url=\Storage::disk('awsS3Bucket')->temporaryUrl(
                $path, \Carbon::now()->addMinutes(30)
            );
            return view('osgc::course.course-content',compact('sectionDet','content','url','sectionId'));   
       }
       
    }
    /**
     * Dispaly test content
     *
     * @param  $request
     * @return json
     */
    public function showTestContent(Request $request)
    {
       $examInputs=array();
       $flag=0;
       $examResult=$this->osgcTestUserResultRepository->getTestAttemptByUserId(\Auth::guard('osgcuser')->user()->id,$request->section_id);
       $examSetting=$this->osgcTestSettingsRepository->getActiveSettingByCourse($request->section_id);//dd($examSetting);
       if(!empty($examSetting)){
        $examInputs = $this->getExamQuestions($request->section_id);
        $updateStatus=$this->osgcUserCourseCompletionRepository->updateUserCourseCompletionStatus($request->section_id,['test_started'=>1]);
       }
    
        $checkExist=$this->osgcUserCourseCompletionRepository->getCourseCompletionById($request->section_id);
        if(isset($examResult->is_exam_pass) && $examResult->is_exam_pass ==1)
        {
            $flag=1;
        }
        return view('osgc::course.test-content',compact('examSetting','examInputs','checkExist','flag'));
       
       
    }
    /**
     * Get questions by sectionId
     * Store test attempt of user.
     * 
     *@param sectionId
     * @return object(questions)
     */

    public function getExamQuestions($sectionId){
        $return['attemptedOptionIds'] = [];
        $examSetting=$this->osgcTestSettingsRepository->getActiveSettingByCourse($sectionId);
        $inputs['test_course_master_id']=$examSetting->id;
        $isDraftExists = $this->osgcTestUserResultRepository->isDraftExists($inputs);
  
        if(!empty($isDraftExists)){
            $return['test_user_result_id'] = $isDraftExists->id;
            
            //Get all attempted question and its options
            $attemptedData = $this->osgcTestUserAttemptedQuestionRepository
            ->getQuestionAndOptionByResultId($return['test_user_result_id'])->toArray();
            
            //Get question id array
            $attemptedQuestionIds = data_get($attemptedData,'*.test_course_question_id');
            $return['questions'] = $this->osgcTestQuestionsRepository->getQuestionByIds($attemptedQuestionIds);
            //Get option id array
            $return['attemptedOptionIds'] = array_unique(data_get($attemptedData,'*.test_course_question_option_id'));
           
        }else{
            $return['questions'] = $this->osgcTestQuestionsRepository->questionDisplay($examSetting);
            $inputs['test_course_question_ids']=$return['questions']->pluck('id')->toArray();
            $attempt = $this->osgcTestUserResultRepository->storeTestAttempt($inputs);
            $return['test_user_result_id']=$attempt->original['test_user_result_id'];
        }
        
        return $return;
    }
    /**
     * save exam data 
     *
     * @param  $request
     * @return json
     */
    public function storeTest(Request $request)
    { 
        $inputs = [];
        $inputs['last_one'] = true;
        $inputs['id'] = $request->input('test_user_result_id');
        $inputs['user_id'] = \Auth::guard('osgcuser')->user()->id;
        $inputs['status'] = 0;
        if(!empty($inputs['id'])){
            $return = $this->osgcTestUserAttemptedQuestionRepository->updateExamAnswers($request->all());
            $this->submitExam($request->input('test_user_result_id'),$request->input('course_id'));
            $examResult=$this->osgcTestUserResultRepository->getTestAttemptByUserId(\Auth::guard('osgcuser')->user()->id,$request->section_id);
            if($examResult->is_exam_pass ==1)
            {
                $testResultArr=array('test_completed'=>1);
            }else{
                $testResultArr=array('test_completed'=>0);
            }
            $this->osgcUserCourseCompletionRepository->updateUserCourseCompletionStatus($request->section_id,$testResultArr);
            $lastCourse = $this->osgcCourseRepository->isLastCourseComplete($request->section_id);//dd($lastCourse);
            
            if($lastCourse == true)
            {
                $certificateExist=$this->osgcCourseRepository->getCourseCertificationByUser($request->input('course_id'));
                if($certificateExist)
                {
                    $data['last_child'] = 2;
                }else{
                    $data['last_child'] = 1;
                }
               
                
            }else{
                $data['last_child'] = 2;
                
            }
            $data['success'] = true;
            $data['message'] = "Answer successfully updated";
            $data['result']=$examResult;
        }else{
            $data['success'] = false;
            $data['message'] = "Faild to updated answer!! Tray again";
        }
       return $data; 
    }
    /**
     * Save Exam
     *
     * @param  $request
     * @return json
     */
    public function submitExam($test_user_result_id,$course_id){
        
        $result = $this->osgcTestUserResultRepository->getById($test_user_result_id);

        $data['total_attempted_questions'] = $this->osgcTestUserAttemptedQuestionRepository->getQuestionAttemptedCount($test_user_result_id);
        $data['total_exam_score'] = $this->osgcTestUserAttemptedQuestionRepository->getRightAnswerCount($test_user_result_id);
        $data['score_percentage'] = ($data['total_exam_score'] / $result->total_questions)*100;
        $data['status']= 1;
        $data['submitted_at']= \Carbon::now();

        if($data['score_percentage'] >= $result->course_pass_percentage){
            $data['is_exam_pass']= 1;
        }

        $exam = $this->osgcTestUserResultRepository->submitExam($test_user_result_id,$data);
        if($exam){
           
            $data['success'] = true;
            $data['message'] = "Successfully submitted";
        
           

        }else{
            $data['success'] = false;
            $data['message'] = "Faild to submit!! Tray again";
        } 
        return $data;
    }

    /**
     * check Course Having Test
     * @return Response
     */
    public function checkCourseHavingTest(Request $request)
    {
        $sectionId = $request->section_id;
        
        $examResult=$this->osgcTestUserResultRepository->getTestAttemptByUserId(\Auth::guard('osgcuser')->user()->id,$sectionId);
        if(isset($examResult->is_exam_pass) && $examResult->is_exam_pass ==1){
                $checkAllsections=$this->osgcUserCourseCompletionRepository->checkAllsectionCompleteUnderHeader($request->section_id);
                $data['success'] = true;
                $data['flag'] = 2;
                $data['header_complete'] = $checkAllsections;
                $data['message'] = "Success";
        }else{
            $this->osgcUserCourseCompletionRepository->updateUserCourseCompletionStatus($request->section_id,['content_completed'=>1,'status'=>1],$request->course_id);
            $result = $this->osgcTestSettingsRepository->getActiveTest($sectionId);
            if(!empty($result)){
                $this->osgcUserCourseCompletionRepository->updateUserCourseCompletionStatus($request->section_id,['test_started'=>0]);
                $this->osgcUserCourseCompletionRepository->updateUserCourseCompletionStatus($request->section_id,['test_completed'=>0]);
            
                $data['success'] = true;
                $data['flag'] = 1;
                $data['message'] = "Success";
            }else{
                $this->osgcUserCourseCompletionRepository->updateUserCourseCompletionStatus($request->section_id,['status'=>1]);
                $data['success'] = true;
                $data['flag'] = 2;
                $data['message'] = "Failed";
            }
            $lastCourse = $this->osgcCourseRepository->isLastCourseComplete($sectionId);//dd($lastCourse);
            if($lastCourse ==true)
            {
                $courseDet=$this->osgcCourseRepository->getCourseSectionDetails($sectionId);
                $certificateExist=$this->osgcCourseRepository->getCourseCertificationByUser($courseDet->course_id);
                //dd($certificateExist);
                if($certificateExist)
                {
                    $data['last_child'] = 2;
                }else{
                    $data['last_child'] = 1;
                    $data['flag'] = 2;// not displaying test if in last course
                }
                
                
            }else{
                $data['last_child'] = 2;
                
            }
            $checkAllsections=$this->osgcUserCourseCompletionRepository->checkAllsectionCompleteUnderHeader($request->section_id);
            $data['header_complete'] = $checkAllsections;
        }
        
        
       return $data; 
    }

   

    /**
     * check Course Having Test
     * @return Response
     */
    public function getCourseCertificate(Request $request)
    {
        $encodedData='';
        $courseId = $request->input('course_id');
        $isExist=$this->osgcCourseRepository->getCourseCertificationByUser($courseId);
        if(empty($isExist)){
            $encodedData=$this->osgcCourseRepository->generatePdf($courseId);
            
        }
        
        return view('osgc::course.course-certificate',compact('encodedData','isExist'));
    }
    /**
     * Download Study Guide
     * @return Response
     */
    public function downloadStudyGuide(Request $request)
    {
        $result=$this->osgcCourseLookupRepository->getStudyGuide($request);
        if($result)
        {
            $file='osgc/pdf/'.$result->file_name;
            $url=$this->osgcCourseRepository->getSignedUrl($file,1);
            return response()->json([
                'status' => 'success',
                'url' => $url
            ]);
            

            
        }
        
        
    }
     /**
     * Download certificate
     * @return Response
     */
    public function downloadCertificate(Request $request)
    {
        $result=$this->osgcCourseRepository->getCourseCertificationByUser($request->course_id);
        if($result)
        {
            $file='osgc/certificate/'.$result->certificate_name;
            $url=$this->osgcCourseRepository->getSignedUrl($file,1);
            return response()->json([
                'status' => 'success',
                'url' => $url
            ]);
            
        }

            
    }
    public function checkContentActive(Request $request)
    {
        $result= $checkCourseCompletion=$this->osgcCourseRepository->checkCourseCompletion($request);//dd($checkCourseCompletion);
        return response()->json([
            'success' => $result,
          
        ]);
    } 
    /**
     * Display Test Result
     *
     * @param  $request
     * @return json
     */   
    public function showTestResult(Request $request)
    {
        $examResult=$this->osgcTestUserResultRepository->getTestAttemptByUserId(\Auth::guard('osgcuser')->user()->id,$request->section_id);
        return view('osgc::course.test-result',compact('examResult')); 
    }  
    public function checkLastCourse(Request $request)
    {
        $lastCourse = $this->osgcCourseRepository->isLastCourseComplete($request->section_id);//dd($lastCourse);
        return response()->json([
            'success' => $lastCourse
        ]);
    }
    /**
     * check study guide exist or not
     *
     * @param  $request
     * @return json
     */
    public function getStudyGuide(Request $request)
    {
        return response()->json($this->osgcCourseLookupRepository->getStudyGuide($request));
    }


}
