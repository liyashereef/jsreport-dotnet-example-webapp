<?php

namespace Modules\LearningAndTraining\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Repositories\TeamRepository;
use Modules\LearningAndTraining\Repositories\TrainingTeamCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseUserRatingRepository;
use Modules\LearningAndTraining\Repositories\CourseContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;

class DashboardController extends Controller
{

    public function __construct(TrainingCourseRepository $course_repo, CourseContentRepository $courseContentRepository, TrainingUserContentRepository $trainingUserContentRepository)
    {
        $this->team_repo = new TeamRepository();
        $this->course_repo = $course_repo;
        $this->user_course_repo = new TrainingUserCourseAllocationRepository();
        $this->trainingCourseUserRatingRepository = new TrainingCourseUserRatingRepository();
        $this->courseContentRepository = $courseContentRepository;
        $this->trainingUserContentRepository=$trainingUserContentRepository;
    }

    public function index()
    {
        $team_count = $this->team_repo->getTotalCount();
        $course_count = $this->course_repo->getTotalCount();

        return view('learningandtraining::admin.dashboard', compact('team_count', 'course_count'));
    }

    public function getCourses()
    {
        return datatables()->of($this->course_repo->getListByAllicationDetails())->toJson();
    }

    public function getCoursesDetails($id)
    {

        $course_details = $this->course_repo->getUserCourseDetails($id);
        //        $data['course_users'] = $this->user_course_repo->getUsersList($id);
        $course_rating = $this->trainingCourseUserRatingRepository->getRatingByCourseId($id);
        return view('learningandtraining::admin.course-details', compact('course_details', 'id', 'course_rating'));
    }

    public function getTrainingCoursesDetails($id)
    {

        $course_details = $this->course_repo->getTrainingUserCourseDetails($id);
//        $data['course_users'] = $this->user_course_repo->getUsersList($id);
        $course_rating = $this->trainingCourseUserRatingRepository->getRatingByCourseId($id);
        return view('learningandtraining::admin.training-user-course-details', compact('course_details', 'id', 'course_rating', 'is_user'));
    }
    public function getCoursesUserDetails($id, $training_user_id = null)
    {

        return datatables()->of($this->user_course_repo->getUsersList($id, $training_user_id))->toJson();
    }

    public function getReports()
    {
        return view('learningandtraining::admin.report');
    }

    public function generateReports()
    {
        return datatables()->of($this->user_course_repo->reportFormat())->toJson();
    }

    public function viewCandidateReports()
    {
        return view('learningandtraining::admin.candidate-report');
    }

    public function generateCandidateReports()
    {
        return datatables()->of($this->user_course_repo->reportCandidateFormat())->toJson();
    }

    public function manualCompletion(Request $request)
    {

        try {
             \DB::beginTransaction();
            $course_complete=$this->user_course_repo->courseCompletion($request->all());
            $content_id_arr=$this->courseContentRepository->getContentById($request->course_id)->pluck('id')->toArray();
            $content_completion= $this->trainingUserContentRepository->contentCompletion($content_id_arr, $request);
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}
