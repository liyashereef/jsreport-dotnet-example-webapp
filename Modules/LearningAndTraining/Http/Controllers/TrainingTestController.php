<?php

namespace Modules\LearningAndTraining\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;

use Modules\LearningAndTraining\Repositories\TrainingTestSettingsRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseUserRatingRepository;
use Modules\LearningAndTraining\Repositories\TestUserResultRepository;
use Modules\LearningAndTraining\Repositories\TestUserAttemptedQuestionRepository;
use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Repositories\TrainingTestRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrainingTestController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $trainingTestSettingsRepository;
    protected $trainingUserCourseAllocationRepository;
    protected $trainingCourseUserRatingRepository;
    protected $testUserResultRepository;
    protected $testUserAttemptedQuestionRepository;
    protected $trainingTestQuestionsRepository;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(
        TrainingCourseRepository $trainingCourseRepository,
        TrainingTestSettingsRepository $trainingTestSettingsRepository,
        TrainingUserCourseAllocationRepository $trainingUserCourseAllocationRepository,
        TrainingCourseUserRatingRepository $trainingCourseUserRatingRepository,
        TestUserResultRepository $testUserResultRepository,
        TestUserAttemptedQuestionRepository $testUserAttemptedQuestionRepository,
        HelperService $helperService,
        TrainingTestRepository $trainingTestRepository
    ) {
        $this->repository = $trainingCourseRepository;
        $this->trainingTestSettingsRepository=$trainingTestSettingsRepository;
        $this->trainingUserCourseAllocationRepository = $trainingUserCourseAllocationRepository;
        $this->trainingCourseUserRatingRepository = $trainingCourseUserRatingRepository;
        $this->testUserResultRepository = $testUserResultRepository;
        $this->testUserAttemptedQuestionRepository = $testUserAttemptedQuestionRepository;
        $this->helperService = $helperService;
        $this->trainingTestRepository=$trainingTestRepository;
    }

    /**
     * Load the resource listing Page
     *@param courseId
     * @return \Illuminate\Http\Response
     */
    public function index($id = null)
    {
        $result = $this->testUserResultRepository->getResultByCourseId($id);
        if (!empty($result)) {
            return $this->getResultDetailById($result->id);
        }
        
        $courseDet =$this->repository->get($id);
        $userCourseDet=$this->trainingUserCourseAllocationRepository->getUserCourseDetByCourse($id);
        $course_rating = $this->trainingCourseUserRatingRepository->getRatingByCourseId($id);
        $examInputs = $this->trainingTestRepository->getExamQuestions($id);

        $examSetting=$this->trainingTestSettingsRepository->getActiveSettingByCourse($id);
        if (isset($userCourseDet['completed_percentage'])) {
            $circleBar['value']=$userCourseDet['completed_percentage']/100;
            $circleBar['perc']=$userCourseDet['completed_percentage'];
        } else {
            $circleBar['value']=1/100;
            $circleBar['perc']=1;
        }
    
        return view('learningandtraining::question', compact('id', 'courseDet', 'course_rating', 'circleBar', 'examSetting', 'examInputs'));
    }

    

    public function store(Request $request)
    {
        
        return $this->trainingTestRepository->save($request);
    }

    

    public function getResultDetailById($id)
    {
      
        $inputs['last_one'] = true;
        $inputs['user_id'] = \Auth::user()->id;
        // $inputs['is_exam_pass'] = true;
        $inputs['ids'] = $id;
        $result = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        // $result_details=$this->testUserResultRepository->getById($id);

        $courseDet =$this->repository->get($result->training_course_id);
        $userCourseDet=$this->trainingUserCourseAllocationRepository->getUserCourseDetByCourse($result->training_course_id);
        $course_rating = $this->trainingCourseUserRatingRepository->getRatingByCourseId($result->training_course_id);
        if (isset($userCourseDet['completed_percentage'])) {
            $circleBar['value']=$userCourseDet['completed_percentage']/100;
            $circleBar['perc']=$userCourseDet['completed_percentage'];
        } else {
            $circleBar['value']=1/100;
            $circleBar['perc']=1;
        }
      

        return view('learningandtraining::exam.exam-resilt-detail', compact('result', 'courseDet', 'circleBar', 'course_rating'));
    }

    public function showAllResults($user_id, $course_id)
    {
        $inputs['user_id']=$user_id;
        $inputs['training_course_id']=$course_id;
        $inputs['status']=1;
        $result = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        return response()->json(['success'=>true,'data'=>$result]);
    }
}
