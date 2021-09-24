<?php


namespace Modules\LearningAndTraining\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\LearningAndTraining\Models\Team;
use Modules\LearningAndTraining\Models\TrainingUserContent;
use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Models\TrainingUserTeamCourseAllocation;
use Illuminate\Support\Carbon;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TestUserResultRepository;
use Modules\LearningAndTraining\Models\User;
use Modules\LearningAndTraining\Models\TestUserResult;
use Modules\LearningAndTraining\Repositories\TrainingCourseUserRatingRepository;

class TrainingUserCourseAllocationRepository
{


    public function __construct()
    {
        $this->user_course_allocation = new TrainingUserCourseAllocation();
        $this->user_team_course_allocation = new TrainingUserTeamCourseAllocationRepository();
        $this->testUserResult = new TestUserResult();
        $this->training_user_course_rating = new TrainingCourseUserRatingRepository();
    }

    /**
     * Get training user allocation course list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->user_course_allocation->select(['id', 'user_id', 'created_by', 'updated_by', 'course_id', 'mandatory', 'recommended', 'completed', 'completed_percentage'])->with('team', 'user', 'created_by', 'updated_by', 'course', 'trainingUserCourseAllocation')->get();
    }

    /**
     * Get user allocation course idss
     *
     * @param $user_id
     * @return object
     */

    public function userAllAllocatedRecordIds($user_id)
    {
        return $this->user_course_allocation->select(['id'])->where('user_id', $user_id)->get();
    }
    /**
     * Get user allocation course idss
     *
     * @param $user_id
     * @return object
     */

    public function getCompletedCourseCount($training_user_id)
    {
        return $this->user_course_allocation->where('training_user_id', $training_user_id)->where('completed', 1)->count();
    }
    

    /**
     * Get user allocation course idss
     *
     * @param $user_id
     * @return object
     */

    public function userAllAllocationByCourseId($course_id)
    {
        return $this->user_course_allocation->where('course_id', $course_id)->get();
    }
    /**
     * Get user allocation course list
     *
     * @param $user_id
     * @return object
     */

    public function getAllUserAllocation($user_id)
    {

        return $this->getAllUserCourseAllocationList($user_id)->map(function ($item) {
            $data_list['course_type_flag'] = 0;
            $data_list = [
                'id' => $item->id,
                'course_title' => $item->course->course_title,
                'alloted_date' => date('M d Y', strtotime($item->created_at)),
                'completed' => $item->completed,
                'completed_percentage' => $item->completed_percentage . '%',
                'completed_date' => '',
                'test_score' => isset($item->course->TestUserSuccessResult) ? $item->course->TestUserSuccessResult->total_exam_score : null,
                'is_exam_pass' => isset($item->course->TestUserSuccessResult) ? (($item->course->TestUserSuccessResult->is_exam_pass == 1) ? 'Passed' : 'Failed') : null,
                'submitted_date' => isset($item->course->TestUserSuccessResult) ? $item->course->TestUserSuccessResult->submitted_at : null,
                'course_id' => $item->course->id,
                'user_id' => $item->user->id,

            ];

            if ($item->completed_date != '') {
                $data_list['completed_date'] = date('M d Y', strtotime($item->completed_date));
            }
            if ($item->mandatory == 1) {
                $data_list['course_type_flag'] = 1;
            }
            if ($item->recommended == 1) {
                $data_list['course_type_flag'] = 2;
            }
            return $data_list;
        });
    }

    public function getAllUserCourseAllocationList($user_id)
    {
        return $this->user_course_allocation->where('user_id', $user_id)->with('course', 'user', 'trainingUserTeamCourseAllocation', 'trainingUserTeamCourseAllocation.team')->with([
            'course.TestUserSuccessResult' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            },
        ])->get();
    }
    /**
     * Store Details
     *
     * @param
     * @return object
     */
    public function store($data)
    {
        return $this->user_course_allocation->create($data);
    }

    /**
     * checking Course Already Allocated or not
     *
     * @param $user_id, $course_id
     * @return object
     */

    public function checkCourseAlreadyAllocated($input)
    {
        if (isset($input['user_id'])) {
            return $this->user_course_allocation
            ->select(
                ['id', 'user_id', 'course_id', 'mandatory','recommended',
                 'completed', 'completed_percentage','manual_completion',
                 'manual_completed_date','deleted_at'
                ]
            )
            ->where('user_id', $input['user_id'])
            ->where('course_id', $input['course_id'])
            ->with('trainingUserTeamCourseAllocation')
            ->orderBy('completed', 'DESC')
            ->withTrashed()
            ->first();
        } else {
            return $this->user_course_allocation
            ->select(
                ['id','user_id','course_id','mandatory','recommended',
                'completed','completed_percentage','manual_completion',
                'manual_completed_date','deleted_at'
                ]
            )
            ->where('training_user_id', $input['training_user_id'])
            ->where('course_id', $input['course_id'])
            ->with('trainingUserTeamCourseAllocation')
            ->first();
        }
    }

    public function checkCourseAllocation($input)
    {
        if (isset($input['user_id'])) {
            return $this->user_course_allocation->select(['id', 'user_id', 'course_id', 'mandatory', 'recommended', 'completed', 'completed_percentage'])
            ->where('user_id', $input['user_id'])
            ->where('course_id', $input['course_id'])
            ->with('trainingUserTeamCourseAllocation')
            ->first();
        } else {
            return $this->user_course_allocation->select(['id','user_id','course_id','mandatory','recommended','completed','completed_percentage'])
            ->where('training_user_id', $input['training_user_id'])
            ->where('course_id', $input['course_id'])
            ->with('trainingUserTeamCourseAllocation')
            ->first();
        }
    }

    /**
     * UN-Allocate Courses by user id
     *
     * @param $user_id
     * @return boolean
     */

    public function userAllUnallocation($user_id)
    {

        $this->user_course_allocation->where('user_id', $user_id)->update(['updated_by' => Auth::user()->id]);
        return $this->user_course_allocation->where('user_id', $user_id)->delete();
    }

    /**
     * UN-Allocate Courses by id
     *
     * @param $id
     * @return boolean
     */

    public function userUnallocation($id)
    {
        $this->user_course_allocation->where('id', $id)->update(['updated_by' => Auth::user()->id]);
        return $this->user_course_allocation->where('id', $id)->delete();
    }

    /**
     * UN-Allocate Courses by course_id
     *
     * @param $course_id
     * @return boolean
     */

    public function teamUnallocation($inputs)
    {
        $this->user_course_allocation->where('course_id', $inputs['course_id'])->update(['updated_by' => Auth::user()->id]);
        return $this->user_course_allocation->where('course_id', $inputs['course_id'])->delete();
    }

    /**
     * fetch user allocated courses by user_id  & team_id
     *
     * @param $user_id , $team_id
     * @return boolean
     */

    public function getUserAllocatedCourseByTeam($data)
    {
        return TrainingUserCourseAllocation::where('user_id', $data['user_id'])
            ->whereHas('trainingUserTeamCourseAllocation', function ($query) use ($data) {
                $query->where('team_id', $data['team_id']);
            })->get()->load('trainingUserTeamCourseAllocation');
    }

    /**
     * Un-Allocate Course From Team Edit
     *Input team_id and course_id(array)
     * @return boolean
     */
    public function unAllocateByTeamIdAndCourseId($inputs)
    {
        $return = 0;
        if (isset($inputs['course_ids_diff']) && sizeof($inputs['course_ids_diff']) >= 1) {
            $data['team_id'] = $inputs['team_id'];
            foreach ($inputs['course_ids_diff'] as $course_id) {
                $data['course_id'] = $course_id;
                $this->unAllocateByCourseIdNadTeamId($data);
            }
            return $return;
        } else {
            return 0;
        }
    }

    /**
     * UN-Allocate courses by course_id & team_id
     *
     * @param $course_id , $team_id, $parent_team_id
     * @return boolean
     */

    public function unAllocateByCourseIdNadTeamId($inputs)
    {
        $return = 0;

        if (isset($inputs['old_parent_team_id'])) {
            $userTeamCourses = $this->user_course_allocation
                ->where('course_id', $inputs['course_id'])
                ->whereHas('trainingUserTeamCourseAllocation', function ($query) use ($inputs) {
                    $query->where('team_id', $inputs['team_id']);
                })
                ->whereHas('TrainingTeamCourseAllocation', function ($query) use ($inputs) {
                    if (isset($inputs['old_parent_team_id'])) {
                        $query->where('parent_team_id', $inputs['old_parent_team_id']);
                    }
                })
                ->with('trainingUserTeamCourseAllocation')->get();
        } else {
            $userTeamCourses = $this->user_course_allocation
                ->where('course_id', $inputs['course_id'])
                ->whereHas('trainingUserTeamCourseAllocation', function ($query) use ($inputs) {
                    $query->where('team_id', $inputs['team_id']);
                })
                ->with('trainingUserTeamCourseAllocation')->get();
        }

        foreach ($userTeamCourses as $userTeamCourse) {
            if (isset($userTeamCourse->trainingUserTeamCourseAllocation) && sizeof($userTeamCourse->trainingUserTeamCourseAllocation) == 1) {
                $delete = $this->userUnallocation($userTeamCourse->id);
                if ($delete) {
                    $delete_contents = ['user_id' => $userTeamCourse->user_id, 'course_id' => $userTeamCourse->course_id];
                    $return = $this->removeByCourseIdAndUserId($delete_contents);
                    $this->user_team_course_allocation->userUnallocation($userTeamCourse->trainingUserTeamCourseAllocation[0]->id);
                }
            } elseif (isset($userTeamCourse->trainingUserTeamCourseAllocation) && sizeof($userTeamCourse->trainingUserTeamCourseAllocation) > 1) {
                foreach ($userTeamCourse->trainingUserTeamCourseAllocation as $t) {
                    if ($t->team_id == $inputs['team_id']) {
                        $return = $this->user_team_course_allocation->userUnallocation($t->id);
                    }
                }
            }
        }
        return $return;
    }

    /**
     * remove course content by user_id & course_id
     *
     * @param $user_id, $course_id
     * @return boolean
     */

    public function removeByCourseIdAndUserId($inputs)
    {
        return TrainingUserContent::where('user_id', $inputs['user_id'])
            ->whereHas('course_content', function ($query) use ($inputs) {
                $query->where('course_id', $inputs['course_id']);
            })->delete();
    }


    /*** On Deleting A Team delete
     * Remove its allocated course and User Allocation.
     */
    public function unAllocateByTeamId($teamIds)
    {
        $return = 0;

        foreach ($teamIds as $team_id) {
            $user_team_courses = $this->user_team_course_allocation->getTrainingUserCourseAllocationByTeamId($team_id);

            foreach ($user_team_courses as $user_team_course) {
                $team_courses = $this->user_team_course_allocation->getByUserCourseAllocationId($user_team_course->training_user_course_allocation_id, $team_id);

                if (!empty($team_courses) && sizeof($team_courses) >= 1) {
                    //                    foreach ($team_courses as $team_course){
                    //                        if($team_course->team_id == $team_id){
                    //                            $return =  $this->user_team_course_allocation->userUnallocation($team_course->id);
                    //                        }
                    //                    }
                    $this->user_team_course_allocation->userUnallocationByTeamId($team_id);
                } else {
                    $this->user_team_course_allocation->userUnallocation($user_team_course->id);
                    $return = $this->userUnallocation($user_team_course->training_user_course_allocation_id);
                }
            }
        }

        return $return;
    }


    /**
     *For list course and completed count.
     * @param $course_id
     * @return object
     */

    public function getUsersList($course_id, $training_user_id = null)
    {
        $active = true;
        return $this->getUserListAllocation($course_id, $training_user_id);
    }

    /**
     *For list course and completed count.
     * @param type $course_id
     * @return object``
     */
    public function getUserListAllocation($course_id, $training_user_id)
    {
        $courses = '';
        return $this->getCourseAlloUsers($course_id, $training_user_id)->map(function ($item, $courses) {

            $data_list['course_type_flag'] = 0;
            $data_list['completed_date'] = '';

            if (isset($item->user_id)) {
                $data_list = [
                'id' => (isset($item->user) ? $item->user->id : null),
                'emp_no' => (isset($item->user) ? $item->user->employee_no : null),
                'emp_name' => (isset($item->user) ? $item->user->full_name : null),
                'emp_email' => (isset($item->user) ? $item->user->email : null),
                'completed' => (isset($item->completed) ? (($item->completed == 1) ? 'Yes' : '') : null),
                'manual_completion' => ($item->manual_completion == 1) ? 'Yes' : 'No',
                'alloted_date' => date('d-m-Y', strtotime($item->created_at)),
                'is_exam_pass' => '',
                'number_attempts' => '',
                'score' => '',
                'attempts_history' => ''
                ];
            } else {
                if (isset($item->trainingUser)) {
                    $user=$item->trainingUser->model_name::where('id', $item->trainingUser->model_id)->first();
                }
                $data_list = [
                'id' => (isset($item->trainingUser)?$item->trainingUser->id:null),
              //  'emp_no' => (isset($item->user)?$item->trainingUser->employee_no:null),
                'emp_name' => (isset($user)?$user['name']:'--'),
                'emp_email' =>(isset($user)? $user['email']:'--'),
                'completed' => (isset($item->completed)?(($item->completed==1)?'Yes':''):null),
                'manual_completion' => ($item->manual_completion==1)?'Yes':'No',
                'alloted_date' => date('d-m-Y', strtotime($item->created_at)),
                'is_exam_pass' => '',
                'number_attempts' => '',
                'score' => '',
                'attempts_history' => ''
                ];
            }

            if ($item->mandatory == 1) {
                $data_list['course_type_flag'] = 1;
                $data_list['course_type'] = 'Mandatory';
            }
            if ($item->recommended == 1) {
                $data_list['course_type_flag'] = 2;
                $data_list['course_type'] = 'Recommended';
            }
            if ($item->completed_date != null) {
                $data_list['completed_date'] = date('d-m-Y', strtotime($item->completed_date));
            }

            // dd($data_list,$item->test_user_success_result);
            if (isset($item->user_id)) {
                if (sizeof($item->TestUserSuccessResult) >= 1) {
                    $data_list['number_attempts'] = sizeof($item->TestUserSuccessResult);
                    $data_list['score'] = round($item->TestUserSuccessResult[0]->score_percentage);
                    $data_list['is_exam_pass'] = $item->TestUserSuccessResult[0]->is_exam_pass;
                    $data_list['attempts_history'] = $item->TestUserSuccessResult;
                }
            } else {
                if (sizeof($item->TestTrainingUserSuccessResult)>=1) {
                    $data_list['number_attempts'] = sizeof($item->TestTrainingUserSuccessResult);
                    $data_list['score'] = round($item->TestTrainingUserSuccessResult[0]->score_percentage);
                    $data_list['is_exam_pass'] = $item->TestTrainingUserSuccessResult[0]->is_exam_pass;
                    $data_list['attempts_history'] = $item->TestTrainingUserSuccessResult;
                }
            }


            return $data_list;
        });
    }


    /**
     *For list course and users.
     * @param $course_id
     * @return object
     */

    public function getCourseAlloUsers($course_id, $training_user_id)
    {
        if ($training_user_id!=null) {
            $relation='TestTrainingUserSuccessResult';
            $user='trainingUser';
            $column='training_user_id';
        } else {
            $relation='TestUserSuccessResult';
            $user= 'user';
            $column='user_id';
        }
        return $this->user_course_allocation
            ->where('course_id', $course_id)
            ->select('id', 'user_id', 'course_id', 'completed', 'completed_date', 'completed_percentage', 'mandatory', 'recommended', 'created_at', 'manual_completion', 'manual_completed_date', 'training_user_id')
            ->whereNotNull($column)
            ->with([
            $relation => function ($que) use ($course_id) {
                    return $que->where('training_course_id', $course_id)
                        ->where('status', 1)
                ->select('id', 'training_course_id', 'user_id', 'test_course_master_id', 'course_pass_percentage', 'score_percentage', 'total_exam_score', 'is_exam_pass', 'submitted_at', 'training_user_id');
            }])
            ->get()->load($user);
    }

    public function save($data)
    {
        if (isset($data['training_user_id']) && $data['training_user_id']!=null) {
            $column='training_user_id';
            $user_id=$data['training_user_id'];
        } else {
            $column='user_id';
            $user_id=\Auth::User()->id;
        }
        $data[$column] = $user_id;
        $data['updated_by'] =  \Auth::User()->id;
        $result = $this->user_course_allocation->where(array('course_id' => $data['course_id'],$column => $user_id))->update($data);
        return $result;
    }
    public function getTodoCount($training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        return $this->user_course_allocation
        ->where($column, $user_id)
            ->where('completed', 0)
            ->where('mandatory', 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }

    public function getUserAssignedCount()
    {
        return $this->user_course_allocation
            ->where('user_id', Auth::id())
            ->where('mandatory', 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }

    public function getRecommendedCount($training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        return $this->user_course_allocation
            ->where($column, $user_id)
            ->where('completed', 0)
            ->where('recommended', 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }

    public function getRecommendedCourseCount()
    {
        return $this->user_course_allocation
            ->where('user_id', Auth::id())
            ->where('recommended', 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }
    public function getCompletedCount($training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        return $this->user_course_allocation
            ->where($column, $user_id)
            ->where('completed', 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }


    public function getMandatoryCompletedCount()
    {
        return $this->user_course_allocation
            ->where('user_id', Auth::id())
            ->where('completed', 1)
            ->where('mandatory', 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }

    public function getOverDueCountCount($training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        return $this->user_course_allocation
            ->where($column, $user_id)
            ->where('completed', 0)
            ->whereHas('course', function ($query) {
                $query->where('course_due_date', '<', date('Y-m-d'));
                $query->where('status', 1);
            })->count();
    }
    public function getTodoMandatoryCount()
    {
        return $this->user_course_allocation
            ->where('user_id', Auth::id())
            ->where('mandatory', 1)
            ->whereHas('course', function ($query) {
                // $query->where('status', 1);
            })->count();
    }

    public function getCourseLibraryCount($training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        /* return $this->user_course_allocation
         ->where('user_id',Auth::id())
         ->count();*/
        return $this->user_course_allocation
            ->where($column, $user_id)
            ->whereHas('course', function ($query) {
                $query->where('add_to_course_library', 0);
                $query->where('status', 1);
            })->count();
    }

    public function getMandatoryCourseLibraryCount()
    {
        /* return $this->user_course_allocation
            ->where('user_id',Auth::id())
            ->count();*/
        return $this->user_course_allocation
            ->where('user_id', Auth::id())
            ->where("mandatory", 1)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->count();
    }

    /**End** Leaner Dash Board Counts */

    public function getRecentAchivements($training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        return $this->user_course_allocation
        ->where($column, $user_id)
            ->where('completed', 1)
            ->orderby('completed_date', 'desc')
            ->select('id', 'user_id', 'course_id', 'completed', 'completed_percentage', 'mandatory', 'recommended', 'created_at', 'completed_date')
            ->with('course')
            ->get();
    }
    public function getRecentAchievementWithPagination($inputs)
    {
        return $this->user_course_allocation
        ->where($inputs['column'], $inputs['user_id'])
            ->where('completed', 1)
            ->orderby('completed_date', 'desc')
            ->select('id', 'user_id', 'course_id', 'completed', 'completed_percentage', 'mandatory', 'recommended', 'created_at', 'completed_date')
            ->with('course')
            ->paginate(4);
    }
    /**Start** Leaner DashBoard Course List */

    public function getDashboardData($course_type, $course_name, $training_user_id = null)
    {
        /*****
         * $course_type ==
         * To – Do = 1
         * Completed = 2
         * Overdue = 3
         * Recommended = 4
         * Course Library = 5
         */

        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        $return = array();
        if (!isset($course_type) || $course_type == '' || $course_type == 1) { //To – Do
            $return = $this->user_course_allocation
            ->where($column, $user_id)
                ->where('completed', 0)
                ->where('mandatory', 1)
                ->select(
                    'id',
                    'user_id',
                    'course_id',
                    'completed',
                    'completed_percentage',
                    'mandatory',
                    'recommended',
                    'created_at'
                )
                ->whereHas('course', function ($query) use ($course_name) {
                    $query->where('status', 1);
                    if (isset($course_name) && $course_name != '') {
                        $query->where('course_title', 'like', '%' . $course_name . '%');
                    }
                })
                ->with('course')
                ->get();
        } elseif ($course_type == 2) { //Completed
            $return = $this->user_course_allocation
            ->where($column, $user_id)
                ->where('completed', 1)
                ->select(
                    'id',
                    'user_id',
                    'course_id',
                    'completed',
                    'completed_percentage',
                    'mandatory',
                    'recommended',
                    'created_at'
                )
                ->whereHas('course', function ($query) use ($course_name) {
                    $query->where('status', 1);
                    if (isset($course_name) && $course_name != '') {
                        $query->where('course_title', 'like', '%' . $course_name . '%');
                    }
                })
                ->with('course')
                ->get();
        } elseif ($course_type == 3) { //Overdue
            return $this->user_course_allocation
            ->where($column, $user_id)
                ->where('completed', 0)
                ->whereHas('course', function ($query) use ($course_name) {
                    $query->where('status', 1);
                    $query->where('course_due_date', '<', date('Y-m-d'));
                    if (isset($course_name) && $course_name != '') {
                        $query->where('course_title', 'like', '%' . $course_name . '%');
                    }
                })
                ->select(
                    'id',
                    'user_id',
                    'course_id',
                    'completed',
                    'completed_percentage',
                    'mandatory',
                    'recommended',
                    'created_at'
                )
                ->with('course')
                ->get();
        } elseif ($course_type == 4) { //Recommended
            $return = $this->user_course_allocation
            ->where($column, $user_id)
                ->where('completed', 0)
                ->where('recommended', 1)
                ->select(
                    'id',
                    'user_id',
                    'course_id',
                    'completed',
                    'completed_percentage',
                    'mandatory',
                    'recommended',
                    'created_at'
                )
                ->whereHas('course', function ($query) use ($course_name) {
                    $query->where('status', 1);
                    if (isset($course_name) && $course_name != '') {
                        $query->where('course_title', 'like', '%' . $course_name . '%');
                    }
                })
                ->with('course')
                ->get();
        } elseif ($course_type == 5) { //Course Library
            $return = $this->user_course_allocation
            ->where($column, $user_id)
                ->select(
                    'id',
                    'user_id',
                    'course_id',
                    'completed',
                    'completed_percentage',
                    'mandatory',
                    'recommended',
                    'created_at'
                )
                ->whereHas('course', function ($query) use ($course_name) {
                    $query->where('status', 1);
                    if (isset($course_name) && $course_name != '') {
                        $query->where('course_title', 'like', '%' . $course_name . '%');
                    }
                    $query->where('add_to_course_library', 0);
                })
                ->with('course')
                ->get();
        }

        return $return;
    }

    /**End** Leaner DashBoard Course List */
    public function getUserCourseDetByCourse($course_id, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = Auth::id();
            $column = 'user_id';
        }
        return $this->user_course_allocation
            ->where('course_id', $course_id)
         ->where($column, $user_id)
            ->select('id', 'user_id', 'course_id', 'completed', 'completed_percentage', 'mandatory', 'recommended', 'created_at', 'training_user_id')
            ->first();
    }

    public function getAllocationAndCompletedCount($inputs)
    {
        $query = $this->user_course_allocation
            ->groupBy('course_id', 'completed')
            ->select('course_id', 'completed', DB::raw('count(*) as data_count'));

        if (!empty($inputs)) {
            if (isset($inputs['course_id'])) {
                $query->where('course_id', $inputs['course_id']);
            }
            if (isset($inputs['user_ids'])) {
                $query->whereIn('user_id', $inputs['user_ids']);
            }

            if (isset($inputs['from_date']) && isset($inputs['to_date'])) {
                $query->whereBetween('created_at', [$inputs['from_date'], $inputs['to_date']]);
            }
        }
        return $query->get()->load('course_with_trashed');
    }


    public function generateReport($candidate_report = false)
    {
        if ($candidate_report) {
            return $this->user_course_allocation->whereNull('deleted_at')
            ->where('training_user_id', '!=', 0)->where('user_id', null)
            ->with(['course'=> function ($que) {
                return $que->select('id', 'reference_code', 'training_category_id', 'course_title', 'course_due_date');
            },
            'trainingUser.recCandidateTrashed' => function ($que) {
                    return $que->select('id', 'name', 'email');
            },
                'trainingUserTeamCourseAllocation' => function ($que) {
                    return $que->select('id', 'team_id', 'training_user_course_allocation_id');
                },
                'trainingUserTeamCourseAllocation.team' => function ($que) {
                    return $que->select('id', 'name', 'parent_team_id');
                }
                ])
                ->get();
        } else {
            return $this->user_course_allocation->whereNull('deleted_at')
            ->where('user_id', '!=', 1)
            ->with(['course'=> function ($que) {
                    return $que->select('id', 'reference_code', 'training_category_id', 'course_title', 'course_due_date');
            },
                'trashed_user' => function ($que) {
                    return $que->select('id', 'first_name', 'last_name', 'email');
                },
                'trashed_user.trashedEmployee' => function ($que) {
                    return $que->select('id', 'user_id', 'employee_no');
                },
                'trainingUserTeamCourseAllocation' => function ($que) {
                    return $que->select('id', 'team_id', 'training_user_course_allocation_id');
                },
                'trainingUserTeamCourseAllocation.team' => function ($que) {
                    return $que->select('id', 'name', 'parent_team_id');
                }
            ])
            ->get();
        }
    }

    /**
     *For list course and completed count.
     * @param type $course_id
     * @return object``
     */
    public function reportFormat()
    {
        $courses = '';
        return $this->generateReport()->map(function ($item, $courses) {
            $data_list['course_type_flag'] = 0;
            $data_list['completed_date'] = '';
            $allocated_teams = [];
            foreach ($item->trainingUserTeamCourseAllocation as $teams) {
                array_push($allocated_teams, $teams->team->name);
            }

            if (!empty($item->course) && !empty($item->trashed_user)) {
                $result = $this->getResult($item->course->id, $item->trashed_user->id);
                $number_attempts = $this->getAttemptCount($item->course->id, $item->trashed_user->id);
                $data_list = [
                    'id' => $item->trashed_user->id,
                    'emp_no' => $item->trashed_user->trashedEmployee->employee_no,
                    'emp_name' => $item->trashed_user->full_name,
                    'emp_email' => $item->trashed_user->email,
                    'completed' => $item->completed,
                    'alloted_date' => date('d-m-Y', strtotime($item->created_at)),
                    'team' => implode(",", $allocated_teams),
                    'course' => $item->course->course_title,
                    'number_attempts' => (isset($number_attempts) && ($number_attempts != 0) ? $number_attempts : null),
                    'is_exam_pass' => (isset($result) ? $result->is_exam_pass : null),
                    'score' => (isset($result) ? round($result->score_percentage) : null),
                ];

                if ($item->completed_date) {
                    $data_list['completed_date'] = date('d-m-Y', strtotime($item->completed_date));
                } else {
                    $data_list['completed_date'] = '';
                }

                if ($item->mandatory == 1) {
                    $data_list['course_type_flag'] = 1;
                }
                if ($item->recommended == 1) {
                    $data_list['course_type_flag'] = 2;
                }
                if ($item->completed_date != null) {
                    $data_list['completed_date'] = date('d-m-Y', strtotime($item->completed_date));
                }
            }
            return $data_list;
        });
    }

    public function reportCandidateFormat()
    {
        $courses = '';
        return $this->generateReport(true)->map(function ($item, $courses) {
            $data_list['course_type_flag'] = 0;
            $data_list['completed_date'] = '';
            $allocated_teams = [];
            foreach ($item->trainingUserTeamCourseAllocation as $teams) {
                array_push($allocated_teams, $teams->team->name);
            }

            if (!empty($item->course) && !empty($item->trainingUser->recCandidateTrashed)) {
                $result = $this->getResult($item->course->id, null, $item->trainingUser->recCandidateTrashed->id);
                $number_attempts = $this->getAttemptCount($item->course->id, null, $item->trainingUser->recCandidateTrashed->id);
                $data_list = [
                    'id' => $item->trainingUser->recCandidateTrashed->id,
                    'name' => $item->trainingUser->recCandidateTrashed->name,
                    'emp_email' => $item->trainingUser->recCandidateTrashed->email,
                    'completed' => $item->completed,
                    'alloted_date' => date('d-m-Y', strtotime($item->created_at)),
                    'team' => implode(",", $allocated_teams),
                    'course' => $item->course->course_title,
                    'number_attempts' => (isset($number_attempts) && ($number_attempts != 0) ? $number_attempts : null),
                    'is_exam_pass' => (isset($result) ? $result->is_exam_pass : null),
                    'score' => (isset($result) ? round($result->score_percentage) : null),
                ];

                if ($item->completed_date) {
                    $data_list['completed_date'] = date('d-m-Y', strtotime($item->completed_date));
                } else {
                    $data_list['completed_date'] = '';
                }

                if ($item->mandatory == 1) {
                    $data_list['course_type_flag'] = 1;
                }
                if ($item->recommended == 1) {
                    $data_list['course_type_flag'] = 2;
                }
                if ($item->completed_date != null) {
                    $data_list['completed_date'] = date('d-m-Y', strtotime($item->completed_date));
                }
            }
            return $data_list;
        });
    }

    public function getResult($courseId, $userId = null, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $column = 'training_user_id';
            $user_id = $training_user_id;
        } else {
            $column = 'user_id';
            $user_id = $userId;
        }
        return $this->testUserResult->where('training_course_id', $courseId)
            ->where($column, $user_id)
            ->orderBy('id', 'DESC')
            ->where('status', 1)
            ->first();
    }

    public function getAttemptCount($courseId, $userId = null, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $column = 'training_user_id';
            $user_id = $training_user_id;
        } else {
            $column = 'user_id';
            $user_id = $userId;
        }
        return $this->testUserResult->where('training_course_id', $courseId)
            ->where($column, $user_id)
            ->where('status', 1)
            ->count();
    }

    public function courseCompletion($data)
    {
        if (isset($data['is_training_user'])) {
            $column='training_user_id';
        } else {
            $column='user_id';
        }
        $course_update=  $this->user_course_allocation->where('course_id', $data['course_id'])->whereIn($column, $data['employee_array'])->update(['completed'=>1,'completed_date'=>\Carbon::now(),'completed_percentage'=>100,'manual_completion'=>1,'manual_completed_date'=>\Carbon::now()]);
        return $course_update;
    }

    public function getKpiData($request)
    {
        $queryStr = $this->user_course_allocation
            ->whereHas('customerEmployeeAllocation', function ($query) use ($request) {
                return $query->where('customer_id', $request['customer_id']);
            })
            ->whereHas('course', function ($query) use ($request) {
                return $query->where('status', 1);
            })
            ->when(!empty($request['user_ids']), function ($query) use ($request) {
                return $query->whereIn('user_id', $request['user_ids']);
            })
            ->select('id', 'user_id', 'course_id', 'completed', 'completed_date');
            // ->get();

        $total = $queryStr->count();
        $completed = $queryStr->where('completed', '!=', 0)->count();
        $percentage = 0;
        if ($completed != 0) {
            $percentage = ($completed / $total) * 100;
        }
        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $percentage
        ];
    }

    public function getListCourse($course_lists, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $course_path = config('globals.recruitmentUrl') . '/training/view-course';
        } else {
            $course_path = route("course-learner.view", "");
        }
        // $course_name = $request->get('search_key');
        $return = '';

        // $course_lists = $this->user_courses->getDashboardData($course_type, $course_name);

        foreach ($course_lists as $course_list) {
            $difference = -1;
            $now = Carbon::parse(date('Y-m-d'));
            if ($course_list->course) {
                //calculate course due date difference in days.
                if ($course_list->course->course_due_date) {
                    $course_due_date = new Carbon($course_list->course->course_due_date);
                    if ($course_due_date >= $now) {
                        $difference = $now->diff($course_due_date)->days;
                    }
                }
                $course_rating = $this->training_user_course_rating->getRatingByCourseId($course_list->course->id);
                $rating_html = "";
                for ($i = 1; $i <= $course_rating; $i++) {
                    $rating_html .= '<span><img src="' . asset('css/training/leaner-dashboard/images/Rating-star-icon.png') . '" alt=""></span>';
                }
                //default image
                $course_image = asset('images/courses_noimage.png');

                //if course image exists replace it with default image.
                if ($course_list->course->course_image) {
                    $base_path = 'LearningAndTraining/course_images/';
                    $file_path = public_path() . '/' . $base_path . $course_list->course->course_image;
                    if (file_exists($file_path)) {
                        $course_image = asset($base_path) . '/' . $course_list->course->course_image;
                    }
                }

                // $return .= '<div class="mt-4 d-flex col-md-3">
                //             <a title="' . $course_list->course->course_title . '" href="' . route("course-learner.view", "") . "/" . $course_list->course->id . '">
                //             <div class="bshadow card-box dashboard-card-nrm d-flex flex-column w-100">';
                // $return .= '<img src="' . $course_image . '" alt="" class="card-intro w-100" />';
                // $return .= '<div class="card-bodycontent card-bodycontent justify-content-between d-flex flex-column">
                //                     <div>
                //                         <h3 class="color-dark font-bold">' . $course_list->course->course_title . '</h3>
                //                         <div class="star-rating">' . $rating_html . '

                //                         </div>
                //                     </div>';

                // if ($difference >= 0) {
                //    $days_display= ($difference > 1)?" days":" day";
                //     $return .= '<span class="due-day color-light">Due in ' . $difference .  $days_display.'</span>';
                // } else {
                //     $return .= '<span class="due-day color-light">Due date not defined</span>';
                // }

                // $return .= '</div>
                //             </div> </a>
                //         </div> ';

                $return .= '<div class="d-flex col-md-4   " style="padding-left:30px !important;padding-right:30px !important;border:solid 0.5px #d3d3d3">';
                $return .= '<div class="container_fluid" style="width:100% !important">';
                $title = strlen($course_list->course->course_title) > 70 ? substr($course_list->course->course_title, 0, 70) . "..." : $course_list->course->course_title;
                $return .= '<div class="row">
                <div class="col-md-12 mt-2" style="min-height:80px">

                <h3 class="color-dark font-bold mt-2" style="font-weight:bold;font-size:18px ;line-height: 1.6;white-space:normal">' . $title . '</h3>
                </div></div>';

                $return .= '<div class="row"><div class="col-md-12 mt-3">';
                $return .= '<img style="height:231px !important;width:100%;" src="' . $course_image . '" alt="" class="card-intro w-100" />';
                $return .= '</div></div>';

                $out = strlen($course_list->course->course_description) > 120 ? substr($course_list->course->course_description, 0, 120) . "..." : $course_list->course->course_description;

                $return .= '<div class="row mt-4" style="height:100px !important; max-height:100px !important; margin-bottom: 3%;">';
                $return .= '<div class="col-md-12 word-wrap">';
                $return .= $out;
                if ($course_list->course->course_duration != null) {
                    $return .= '<div style="margin-top: 2%;">';
                    $return .= 'Duration : '.$course_list->course->course_duration.'  minutes';
                    $return .= '</div>';
                }
                $return .= '</div> </div>';

                // $return .= '<div class="row mt-4" style="height:100px !important;max-height:100px !important"><div class="col-md-12 word-wrap"> ';
                // $return .= $course_list->course->course_duration;
                // $return .= '  Minutes </div></div>';

                $return .= '<div class="star-rating" style="min-height:20px !important;margin-top:8px;">' . $rating_html . '

                                        </div>';
                $return .= '<div class="row mt-2" "><div class="col-md-12">';
                if ($difference >= 0) {
                    $days_display = ($difference > 1) ? " days" : " day";
                    $return .= '<span class="due-day color-light">Due in ' . $difference . $days_display . '</span>';
                } else {
                    if ($course_list->course->course_due_date) {
                        $return .= '<span class="due-day color-light">Due date : ' . Carbon::parse($course_list->course->course_due_date)->format('M d, Y') . ' </span>';
                    } else {
                        $return .= '<span class="due-day color-light">Due date not defined</span>';
                    }
                }
                $return .= '</div></div>';

                $return .= '<div class="row mt-2 mb-3">
                ';
                //$return .= '<a class=" mt-2 ml-3 p-2  rounded shadow  readmore" style="border:solid 0.5px #d3d3d3;background:white;color:#F1502B;font-weight:bold" title="' . $course_list->course->course_title . '" href="' . route("course-learner.view", "") . "/" . $course_list->course->id . '">';
                $return .= '<a class=" mt-2 ml-3 p-2  rounded shadow  readmore" style="border:solid 0.5px #d3d3d3;background:white;color:#F1502B;font-weight:bold" title="' . $course_list->course->course_title . '" href="' . $course_path . "/" . $course_list->course->id .'">';

                $return .= 'Read More
                <i class="fa fa-angle-double-right ml-2" aria-hidden="true"></i>
                </a></div>';

                $return .= '</div>';

                $return .= '</div>';
            }
        }

        return $return;
    }


    public function getCompletedCourses($training_user_id = null)
    {
        $return = array();
        if ($training_user_id!=null) {
            $inputs['user_id'] = $training_user_id;
            $inputs['column'] = 'training_user_id';
        } else {
            $inputs['user_id'] = Auth::id();
            $inputs['column'] = 'user_id';
        }
        $recent_achievements = $this->getRecentAchievementWithPagination($inputs);
        $return['html_view'] = '';
        foreach ($recent_achievements as $achievement) {
            $return['html_view'] .= '<div class="row mb-2">
                                            <div class="col-md-12 d-flex">
                                                <img src="' . asset('css/training/leaner-dashboard/images/Achievements-icon.png') . '" alt="" class="badge-ico" />
                                                <div>
                                                    <h3 class="color-dark font-bold mb-1">' . $achievement->course->course_title . '</h3>
                                                    <span class="due-day color-light">' . date('l M d, Y', strtotime($achievement->completed_date)) . '</span>
                                                </div>
                                            </div>
                                        </div>';
        }

        $return['currentPage'] = $recent_achievements->currentPage();
        $return['nextPageUrl'] = $recent_achievements->nextPageUrl();
        if ($recent_achievements->nextPageUrl() != '') {
            $return['nextPage'] = $recent_achievements->currentPage() + 1;
        } else {
            $return['nextPage'] = '';
        }
        return $return;
    }

    public function restoreDeleted($allocationId, $inputs = null)
    {
        $result = $this->user_course_allocation
        ->withTrashed()
        ->find($allocationId)
        ->restore();
        if (is_array($inputs)) {
            $this->user_course_allocation->where('id', $allocationId)->update($inputs);
        }
        return $result;
    }
}
