<?php

namespace Modules\LearningAndTraining\Repositories;

use Modules\LearningAndTraining\Repositories\TestUserAttemptedQuestionRepository;
use Modules\LearningAndTraining\Repositories\TestUserResultRepository;
use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Repositories\TrainingCourseUserRatingRepository;
use Modules\LearningAndTraining\Repositories\TrainingTestSettingsRepository;
use Modules\LearningAndTraining\Repositories\TrainingTestQuestionsRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\Recruitment\Repositories\RecCandidateTrackingRepository;
use Modules\LearningAndTraining\Models\TrainingUser;
use Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation;
use Modules\Recruitment\Models\RecCandidateJobDetails;

class TrainingTestRepository
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
    public function __construct(
        TestUserResultRepository $testUserResultRepository,
        TestUserAttemptedQuestionRepository $testUserAttemptedQuestionRepository,
        TrainingCourseUserRatingRepository $trainingCourseUserRatingRepository,
        TrainingTestSettingsRepository $trainingTestSettingsRepository,
        TrainingTestQuestionsRepository $trainingTestQuestionsRepository,
        TrainingCourseRepository $trainingCourseRepository,
        TrainingUserCourseAllocationRepository $trainingUserCourseAllocationRepository,
        RecCandidateTrackingRepository $recCandidateTrackingRepository
    ) {

        $this->testUserResultRepository=$testUserResultRepository;
        $this->testUserAttemptedQuestionRepository=$testUserAttemptedQuestionRepository;
        $this->trainingCourseUserRatingRepository = $trainingCourseUserRatingRepository;
        $this->trainingTestSettingsRepository=$trainingTestSettingsRepository;
        $this->trainingTestQuestionsRepository = $trainingTestQuestionsRepository;
        $this->trainingCourseRepository = $trainingCourseRepository;
        $this->trainingUserCourseAllocationRepository = $trainingUserCourseAllocationRepository;
        $this->recCandidateTrackingRepository = $recCandidateTrackingRepository;
    }

    

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request, $training_user_id = null)
    {

        if ($training_user_id!=null) {
            $training_user_id = $training_user_id;
            $user_id = null;
        } else {
            $user_id = \Auth::user()->id;
            $training_user_id = null;
        }
        $inputs = [];
        $inputs['last_one'] = true;
        $inputs['id'] = $request->input('test_user_result_id');
        $inputs['user_id'] =  $user_id;
        $inputs['training_user_id'] =  $training_user_id;
        $inputs['status'] = 0;
        $result = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        if (!empty($result)) {
            $return = $this->testUserAttemptedQuestionRepository->updateExamAnswers($request->all());
           
            if ($request->input('final_submit') == 1) {
                $this->submitExam($request->input('test_user_result_id'), $request->input('course_id'), $training_user_id);
            }
           
            $data['success'] = true;
            $data['message'] = "Answer successfully updated";
        } else {
            $data['success'] = false;
            $data['message'] = "Faild to updated answer!! Tray again";
        }
        return $data;
    }

    public function submitExam($test_user_result_id, $course_id, $training_user_id = null)
    {

        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = \Auth::user()->id;
            $column = 'user_id';
        }
        
        $result = $this->testUserResultRepository->getById($test_user_result_id);

        $data['total_attempted_questions'] = $this->testUserAttemptedQuestionRepository->getQuestionAttemptedCount($test_user_result_id);
        $data['total_exam_score'] = $this->testUserAttemptedQuestionRepository->getRightAnswerCount($test_user_result_id);
        $data['score_percentage'] = ($data['total_exam_score'] / $result->total_questions)*100;
        $data['status']= 1;
        $data['submitted_at']= \Carbon::now();

        if ($data['score_percentage'] >= $result->course_pass_percentage) {
            $data['is_exam_pass']= 1;
        }

        $exam = $this->testUserResultRepository->submitExam($test_user_result_id, $data);
        if ($exam) {
            if ($data['score_percentage'] >= $result->course_pass_percentage) {
                $courseUpdate = TrainingUserCourseAllocation::where($column, $user_id)
                ->where('course_id', $course_id)
                ->update(['completed'=>1,'completed_date'=>\Carbon::now()]);
                if (isset($training_user_id)) {
                    $trainingUserDetails=TrainingUser::find($training_user_id);

                    $mandatory_course_arr=TrainingTeamCourseAllocation::where('team_id', config('globals.rec_training_id'))->where('mandatory', 1)->whereHas('training_course', function ($q) {
                        $q->where('status', 1);
                    })->pluck('course_id')->toArray();
                    $candidate_completed=$this->trainingUserCourseAllocationRepository->getCompletedCourseCount($training_user_id);
                    if ($candidate_completed>=count($mandatory_course_arr)) {
                         $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $trainingUserDetails->model_id)->first();
                        $deleteOldCandidateTracking=$this->recCandidateTrackingRepository->deleteOldCandidateTracking($trainingUserDetails->model_id, "core_training_completed");
                        if ($job_details) {
                            $this->recCandidateTrackingRepository->saveTracking($trainingUserDetails->model_id, "core_training_completed", false, $job_details->job_id);
                        } else {
                            $this->recCandidateTrackingRepository->saveTracking($trainingUserDetails->model_id, "core_training_completed", false);
                        }
                    }
                }
            }

            $data['success'] = true;
            $data['message'] = "Successfully submitted";
        
            $this->getResultDetailById($test_user_result_id, $training_user_id);
        } else {
            $data['success'] = false;
            $data['message'] = "Faild to submit!! Tray again";
        }
        return $data;
    }

    public function getResultDetailById($id, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $inputs['training_user_id']  = $training_user_id;
            $inputs['user_id'] = null;
        } else {
            $inputs['user_id']  = \Auth::user()->id;
            $inputs['training_user_id']  = null;
        }

        $inputs['last_one'] = true;
        // $inputs['is_exam_pass'] = true;
        $inputs['ids'] = $id;
        $result = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        // $result_details=$this->testUserResultRepository->getById($id);

        $courseDet =$this->trainingCourseRepository->get($result->training_course_id);
        $userCourseDet=$this->trainingUserCourseAllocationRepository->getUserCourseDetByCourse($result->training_course_id, $training_user_id);
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

    public function getResultDetailsArr($id, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = \Auth::user()->id;
            $column = 'user_id';
        }

        $inputs['last_one'] = true;
        $inputs[$column] = $user_id;
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
        return ['result'=>$result, 'courseDet'=>$courseDet,'circleBar'=>$circleBar,'course_rating'=>$course_rating];
    }

    /**
     * Get questions by course_id
     * Store test attempt of user.
     *
     *@param courseId
     * @return object(questions)
     */



    public function getExamQuestions($courseId, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $inputs['training_user_id']  = $training_user_id;
            $inputs['user_id'] = null;
        } else {
            $inputs['user_id']  = \Auth::user()->id;
            $inputs['training_user_id']  = null;
        }
        $return['attemptedOptionIds'] = [];
        $examSetting=$this->trainingTestSettingsRepository->getActiveSettingByCourse($courseId);
        
        $inputs['test_course_master_id']=$examSetting->id;
        $isDraftExists = $this->testUserResultRepository->isDraftExists($inputs);
  
        if (!empty($isDraftExists) && !empty($isDraftExists->original['data'])) {
            $return['test_user_result_id'] = $isDraftExists->original['data']->id;
            
            //Get all attempted question and its options
            $attemptedData = $this->testUserAttemptedQuestionRepository
            ->getQuestionAndOptionByResultId($return['test_user_result_id'])->toArray();
            
            //Get question id array
            $attemptedQuestionIds = data_get($attemptedData, '*.test_course_question_id');
            $return['questions'] = $this->trainingTestQuestionsRepository->getQuestionByIds($attemptedQuestionIds);
            //Get option id array
            $return['attemptedOptionIds'] = array_unique(data_get($attemptedData, '*.test_course_question_option_id'));
        } else {
            $return['questions'] = $this->trainingTestQuestionsRepository->questionDisplay($examSetting);
            $inputs['test_course_question_ids']=$return['questions']->pluck('id')->toArray();
            $attempt = $this->testUserResultRepository->storeTestAttempt($inputs);
            $return['test_user_result_id']=$attempt->original['test_user_result_id'];
        }
        return $return;
    }
}
