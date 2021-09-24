<?php

namespace Modules\LearningAndTraining\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\LearningAndTraining\Models\User;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Illuminate\Support\Facades\Auth;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamRepositories;
use Modules\LearningAndTraining\Repositories\TeamRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;

class EmployeeAllocationRepository
{

    protected $user_repository;
    protected $role_repository;
    protected $helperService;

    /**
     * Create a new EmployeeAllocationRepository instance.
     */
    public function __construct()
    {
        $this->user_repository = new UserRepository();
        $this->role_repository = new RolesAndPermissionRepository();
        $this->helperService = new HelperService();
        $this->user_team_repository = new TrainingUserTeamRepositories();
        $this->team_repository = new TeamRepository();
        $this->user_course_allocation = new TrainingUserCourseAllocationRepository();
        $this->user_course_contents = new TrainingUserContentRepository();
        $this->user_team_course_allocation_repository = new TrainingUserTeamCourseAllocationRepository();
    }


    /** For Employee Allocation For Team in Leaner Module
     * Get users that can report to a role
     */
    public function getUsersList()
    {
        $active = true;
        return $this->getUserList($active);
    }

    /**
     *For Employee Allocation For Team in Leaner Module
     * Function to prepare values for allocation table
     */
    public function getUserListAllocation()
    {
        $courses = '';
//        return $this->getUsersList();
        return $this->getUsersList()->map(function ($item, $courses) {
            $data_list = [
                'id' => $item->id,
                'emp_no' => $item->employee->employee_no,
                'emp_name' => $item->full_name,
                'emp_email' => $item->email,
            ];
            if (isset($item->trainingUserTeam) && sizeof($item->trainingUserTeam)>=1) {
                $courses = '';
                foreach ($item->trainingUserTeam as $i) {
                    $courses .= $i->team->name.', ';
                }
                $data_list['team'] = rtrim($courses, ', ');
            } else {
                $data_list['team'] = '';
            }

            return $data_list;
        });
    }


    /**
     *
     * Get user list
     */
    public function getUserList($active = null, $role = null, $supervisor_id = null, $role_except = null)
    {
        $user_query_obj = User::select(DB::raw("*, CONCAT(first_name,' ',COALESCE(last_name,'')) as full_name"))
            ->with('trainingUserTeam.team');

        $user_query_obj->when(($active != null), function ($query) use ($active) {
            return $query->where('active', "=", $active);
        });

        return $user_query_obj->get();
    }

    public function saveAllocation($request)
    {

         $inputs = array();
        $alreadyAllottedTeam = array();
        $is_recruitment=$request->has('is_recruitment')?1:0;
        if ($request->has(['user_course_allocation_id']) && $request->get('user_course_allocation_id') != '' || $request->get('user_course_allocation_id') != null) {
               $inputs['updated_by'] = Auth::id();
        } else {
            if ($request->has(['employee_ids', 'team_ids']) && sizeof(json_decode($request->get('employee_ids'))) >= 1 && sizeof(json_decode($request->get('team_ids'))) >= 1) {
                foreach (json_decode($request->get('employee_ids')) as $user_id) {
                    foreach (json_decode($request->get('team_ids')) as $team_id) {
                        $user_team_input['team_id'] = $team_id;
                        if ($is_recruitment) {
                            $user_team_input['training_user_id'] = $user_id;
                        } else {
                            $user_team_input['user_id'] = $user_id;
                        }
                        /******* START - Checking team already added to user by passing parameters as team_id and user_id    */
                        $user_already_allocated = $this->user_team_repository->checkTeamAlreadyAllocated($user_team_input);
                        if ($user_already_allocated <= 0) {
                            /***START***  - Storing team_id and user_id to training_user_teams  */
                            $user_team = $this->user_team_repository->store($user_team_input);
                            /***END***  - Storing team_id and user_id to training_user_teams  */

                            if ($user_team) {
                                $team_courses = $this->team_repository->getCoursesByTeamId($team_id);

                                if (sizeof($team_courses) >= 1) {
                                    foreach ($team_courses as $team_course) {
                                        $inputs['created_by'] = Auth::id();
                                        if ($is_recruitment) {
                                            $inputs['training_user_id'] = $user_id;
                                        } else {
                                            $inputs['user_id'] = $user_id;
                                        }
                                        $inputs['course_id'] = $team_course->course_id;
                                        /******* Checking course already added to the User  **/
                                        $is_course_already_allocated = array();
                                        $is_course_already_allocated = $this->user_course_allocation->checkCourseAlreadyAllocated($inputs);
                                        // if(isset($is_course_already_allocated) && sizeof($is_course_already_allocated)>=1){
                                        if (isset($is_course_already_allocated)) { //dd($is_course_already_allocated);

                                            /***START**** If course already added to employee,
                                                 * Checking for Course from allocating team or not.
                                                 * If not add team id ageniest to the employee allocation record
                                                 **/
                                            //                                                if (!empty($is_course_already_allocated['TrainingUserTeamCourseAllocation']) && sizeof($is_course_already_allocated['TrainingUserTeamCourseAllocation'])>=1) {
                                            if (isset($is_course_already_allocated['TrainingUserTeamCourseAllocation'])) {
                                                foreach ($is_course_already_allocated['TrainingUserTeamCourseAllocation'] as $teamCourseAlloted) {
                                                    array_push($alreadyAllottedTeam, $teamCourseAlloted->team_id);
                                                }

                                                if (in_array($team_id, $alreadyAllottedTeam)) {
                                                } else {
                                                    $user_team_course['training_user_course_allocation_id'] = $is_course_already_allocated->id;
                                                    $user_team_course['team_id'] = $team_id;
                                                    $this->user_team_course_allocation_repository->store($user_team_course);
                                                }
                                            }

                                            if(!is_null($is_course_already_allocated->deleted_at)){

                                                $restoreInputs['mandatory'] = 0;
                                                $restoreInputs['recommended'] = 0;
                                                if ($team_course->mandatory == 1) {
                                                    $restoreInputs['mandatory'] = 1;
                                                }
                                                if ($team_course->recommended == 1) {
                                                    $restoreInputs['recommended'] = 1;
                                                }

                                               $restore = $this->user_course_allocation->restoreDeleted($is_course_already_allocated->id,$restoreInputs);
                                               if($restore){
                                                $user_contents_input = ['user_id' => $user_id, 'course_id' => $team_course->course_id];
                                                $this->user_course_contents->restoreDeletedContent($user_contents_input);
                                               }

                                            }

                                        } else {
                                            /***START****  Course adding to employee,
                                                 * Adding Team Id ageniest to the user allocation record
                                                 **/
                                            if ($team_course->mandatory == 1) {
                                                $inputs['mandatory'] = 1;
                                            } else {
                                                $inputs['mandatory'] = 0;
                                            }

                                            if ($team_course->recommended == 1) {
                                                $inputs['recommended'] = 1;
                                            } else {
                                                $inputs['recommended'] = 0;
                                            }

                                            $user_course_allocation = $this->user_course_allocation->store($inputs);
                                            if ($user_course_allocation) {
                                                if ($is_recruitment) {
                                                    $user_contents_input = ['training_user_id' => $user_id, 'course_id' => $team_course->course_id];
                                                } else {
                                                    $user_contents_input = ['user_id' => $user_id, 'course_id' => $team_course->course_id];
                                                }

                                                $this->user_course_contents->store($user_contents_input);
                                                $user_team_course['training_user_course_allocation_id'] = $user_course_allocation->id;
                                                $user_team_course['team_id'] = $team_id; //dd($user_team_course);

                                                $this->user_team_course_allocation_repository->store($user_team_course);
                                            }
                                        }
                                        /***START****  Course adding to employee,
                                             * Adding Team Id ageniest to the user allocation record
                                             **/
                                        $inputs = array();
                                        $user_team_course = array();
                                    }
                                }
                            }
                        } else {
                        }
                    }
                }
            }
        }
    }
}
