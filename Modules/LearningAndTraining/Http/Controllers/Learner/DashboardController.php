<?php

namespace Modules\LearningAndTraining\Http\Controllers\Learner;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->user_courses = new TrainingUserCourseAllocationRepository();
       // $this->training_user_course_rating = new TrainingCourseUserRatingRepository();
    }

    public function index()
    {
        $todo_count = $this->user_courses->getTodoCount();
        $recommended_count = $this->user_courses->getRecommendedCount();
        $completed_count = $this->user_courses->getCompletedCount();
        $over_due_count = $this->user_courses->getOverDueCountCount();
        $total_course_library = $this->user_courses->getCourseLibraryCount();
        $recent_achievements = $this->user_courses->getRecentAchivements();
        return view('learningandtraining::learner.dashboard', compact('todo_count', 'recommended_count', 'completed_count', 'over_due_count', 'total_course_library', 'recent_achievements'));
    }

    public function getCourseList($course_type, Request $request)
    {
        /*****
         * $course_type ==
         * To – Do = 1
         * Completed = 2
         * Overdue = 3
         * Recommended = 4
         * Course Library = 5
         */
        $course_name = $request->get('search_key');
        $course_lists = $this->user_courses->getDashboardData($course_type, $course_name);
        return $this->user_courses->getListCourse($course_lists);
    }

    /**
     * Recent Achievement courses.
     */
    public function getCompletedCourses()
    {
        return $this->user_courses->getCompletedCourses();
    }
}
