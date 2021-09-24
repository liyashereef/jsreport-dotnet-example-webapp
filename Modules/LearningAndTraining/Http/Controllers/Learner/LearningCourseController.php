<?php

namespace Modules\LearningAndTraining\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\LearningAndTraining\Http\Requests\TrainingCourseRequest;
use Modules\LearningAndTraining\Repositories\TrainingCategoryRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\CourseContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseUserRatingRepository;
use Modules\LearningAndTraining\Repositories\TrainingTestSettingsRepository;
use Modules\LearningAndTraining\Repositories\TestUserResultRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LearningCourseController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $trainingCategoryRepository;
    protected $courseContentRepository;
    protected $trainingUserContentRepository;
    protected $trainingUserCourseAllocationRepository;
    protected $trainingCourseUserRatingRepository;
    protected $trainingTestSettingsRepository;
    protected $testUserResultRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(TrainingCourseRepository $trainingCourseRepository, TrainingCategoryRepository $trainingCategoryRepository, CourseContentRepository $courseContentRepository, TrainingUserContentRepository $trainingUserContentRepository, TrainingUserCourseAllocationRepository $trainingUserCourseAllocationRepository, TrainingCourseUserRatingRepository $trainingCourseUserRatingRepository, HelperService $helperService, TrainingTestSettingsRepository $trainingTestSettingsRepository, TestUserResultRepository $testUserResultRepository)
    {
        $this->repository = $trainingCourseRepository;
        $this->trainingCategoryRepository = $trainingCategoryRepository;
        $this->courseContentRepository = $courseContentRepository;
        $this->trainingUserContentRepository = $trainingUserContentRepository;
        $this->trainingUserCourseAllocationRepository = $trainingUserCourseAllocationRepository;
        $this->trainingCourseUserRatingRepository = $trainingCourseUserRatingRepository;
        $this->helperService = $helperService;
        $this->trainingTestSettingsRepository = $trainingTestSettingsRepository;
        $this->testUserResultRepository = $testUserResultRepository;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryList = $this->trainingCategoryRepository->getList();
        //return view('learningandtraining::learner.learner-course', compact('categoryList'));
    }

    public function view($id)
    {
        $courseDet = $this->repository->get($id);
        $courseContents = $this->courseContentRepository->getContentByCourse($id);
        $userCourseDet = $this->trainingUserCourseAllocationRepository->getUserCourseDetByCourse($id);
        $course_rating = $this->trainingCourseUserRatingRepository->getRatingByCourseId($id);
        $count_of_active_test = $this->trainingTestSettingsRepository->hasActiveTest($id);
        $active_test = $this->trainingTestSettingsRepository->getActiveTest($id);
        if (isset($active_test)) {
            // $inputs['last_one'] = true;
            // $inputs['user_id'] = \Auth::user()->id;
            // $inputs['is_exam_pass'] = true;
            // $inputs['training_course_id'] = $id;
            $exam_results = $this->testUserResultRepository->getResultByCourseId($id);

            //$exam_results=$this->testUserResultRepository->getStatus($id,$active_test->id);
        } else {
            $exam_results = null;
        }
        if (isset($userCourseDet['completed_percentage'])) {
            $circleBar['value'] = $userCourseDet['completed_percentage'] / 100;
            $circleBar['perc'] = $userCourseDet['completed_percentage'];
        } else {
            $circleBar['value'] = 1 / 100;
            $circleBar['perc'] = 1;
        }
        $has_previous_attempt = $this->testUserResultRepository->previousAttemptExist($id);
        return view('learningandtraining::learner.course', compact('courseDet', 'courseContents', 'circleBar', 'course_rating', 'count_of_active_test', 'exam_results', 'has_previous_attempt'));
    }

    public function videoView($id)
    {
        $user_id = \Auth::User()->id;
        $courseContentsDet = $this->courseContentRepository->get($id);
        // $userContents = $this->trainingUserContentRepository->getDataByUserIdAndContentId($user_id,$id);
        $courseContentsNextDet = $this->courseContentRepository->getNextContentByCourse($courseContentsDet->course_id, $id);
        if (isset($courseContentsNextDet[0])) {
            $nextContentId = $courseContentsNextDet[0]->id;
            $nextContentType = $courseContentsNextDet[0]->content_type_id;
        } else {
            $nextContentId = 0;
            $nextContentType = 0;
        }
        $videoLink = "https://s3." . config('filesystems.disks.s3.region') . ".amazonaws.com/" . config('filesystems.disks.s3.bucket') . "/video/" . $courseContentsDet->value;
        // $videoLink = "https://s3.ca-central-1.amazonaws.com/cgltraining/video/WHIMS-1573688824.m4v";
        return view('learningandtraining::learner.course-video', compact(
            'courseContentsDet',
            'nextContentId',
            'nextContentType',
            'videoLink'
        ));
    }

    public function imageView($id)
    {
        $user_id = \Auth::User()->id;
        $courseContentsDet = $this->courseContentRepository->get($id);
        // $userContents = $this->trainingUserContentRepository->getDataByUserIdAndContentId($user_id,$id);
        $courseContentsNextDet = $this->courseContentRepository->getNextContentByCourse($courseContentsDet->course_id, $id);
        if (isset($courseContentsNextDet[0])) {
            $nextContentId = $courseContentsNextDet[0]->id;
            $nextContentType = $courseContentsNextDet[0]->content_type_id;
        } else {
            $nextContentId = 0;
            $nextContentType = 0;
        }
        return view('learningandtraining::learner.course-image', compact('courseContentsDet',
        'nextContentId', 'nextContentType'));
    }

    public function pdfView($id)
    {
        $user_id = \Auth::User()->id;
        $courseContentsDet = $this->courseContentRepository->get($id);
        $userContents = $this->trainingUserContentRepository->getDataByUserIdAndContentId($user_id,$id);
        $courseContentsNextDet = $this->courseContentRepository->getNextContentByCourse($courseContentsDet->course_id, $id);
        if (isset($courseContentsNextDet[0])) {
            $nextContentId = $courseContentsNextDet[0]->id;
            $nextContentType = $courseContentsNextDet[0]->content_type_id;
        } else {
            $nextContentId = 0;
            $nextContentType = 0;
        }
        return view('learningandtraining::learner.course-pdf', compact('courseContentsDet','userContents', 'nextContentId', 'nextContentType'));
    }

    public function contentUpdate(Request $request)
    {
        try {
            \DB::beginTransaction();

            $content_id = $request->get('content_id');
            $user_id = \Auth::User()->id;
            $userCourse = [];
            $completed = null;
            $success = false;
            $userContents = $this->trainingUserContentRepository->getDataByUserIdAndContentId($user_id,$content_id);
            if($userContents->completed == 0){
                $trainingUserContent = $this->trainingUserContentRepository->save($request->all());
                $contentsDet = $this->courseContentRepository->get($content_id);
                $contentsCount = $this->courseContentRepository->getCountByCourseId($contentsDet->course_id);
                $completedCourseContentCount = $this->courseContentRepository->getCompletedContentCountByCourseId($contentsDet->course_id);
                $completedCoursePercentage = ($completedCourseContentCount / $contentsCount) * 100;
                $userCourseData['course_id'] = $contentsDet->course_id;
                $userCourseData['completed_percentage'] = round($completedCoursePercentage, 2);
                $count_of_active_test = $this->trainingTestSettingsRepository->hasActiveTest($contentsDet->course_id);
                if ($completedCoursePercentage == 100 && $count_of_active_test == 0) {
                    $userCourseData['completed'] = 1;
                    $userCourseData['completed_date'] = Carbon::now();
                    $completed = "true";
                } else {
                    $completed = "false";
                }
                $userCourse = $this->trainingUserCourseAllocationRepository->save($userCourseData);
                $success = true;
            }
            \DB::commit();
            return response()->json(array('success' => $success, 'data' => $userCourse, 'completed' => $completed));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function courseRating(Request $request)
    {
        $trainingCourseUserRating = $this->trainingCourseUserRatingRepository->save($request->all());
        return response()->json(array('success' => 'true', 'data' => $trainingCourseUserRating));
    }


    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\TrainingCourseRequest $request
     * @return json
     */
    public function store(TrainingCourseRequest $request)
    {
        try {
            \DB::beginTransaction();
            if (!$request->has('status')) {
                $request->merge(['status' => 0]);
            }
            $course = $this->repository->save($request->all());
            if ($request->hasFile('course_file')) {
                $course = $this->repository->uploadFile($request->all(), $course);
            }
            \DB::commit();
            return response()->json(array('success' => 'true', 'data' => $course));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */

    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $course_delete = $this->repository->delete($id);
            \DB::commit();
            if ($course_delete == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
