<?php

namespace Modules\LearningAndTraining\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Modules\LearningAndTraining\Models\TrainingUserContent;
use Modules\LearningAndTraining\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
//use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Repositories\TeamRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamRepositories;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;

class EmployeeAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    protected $helperService, $user_course_allocation, $team_repository, $user_repository, $user_team_course_allocation_repository, $user_team_repository, $employee_allocation;

    public function __construct()
    {
        $this->helperService = new HelperService();
        $this->user_course_allocation = new TrainingUserCourseAllocationRepository();
        $this->team_repository = new TeamRepository();
        $this->user_repository = new UserRepository();
        $this->user_team_course_allocation_repository = new TrainingUserTeamCourseAllocationRepository();
        $this->user_team_repository = new TrainingUserTeamRepositories();
        $this->employee_allocation = new EmployeeAllocationRepository();
        $this->user_course_contents = new TrainingUserContentRepository();
    }

    public function index()
    {
        $team_list = $this->team_repository->getAllWithSubTeam();
        return view('learningandtraining::admin.team.allocation', compact('team_list'));
    }


    /**
     * List of users that can be allocated
     * @param
     * @return
     */
    public function getAllocationList()
    {
        $list_data = $this->employee_allocation->getUserListAllocation();
        return datatables()->of($list_data)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('learningandtraining::create');
    }

    /**
     * Store - Allocating users to Team.
     *
     * @param  \App\Models\TrainingUserTeam  $team_ids, $user_ids
     * @return \Illuminate\Http\Response
     */
    public function allocate(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->employee_allocation->saveAllocation($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the allocated resource from storage.
     *
     * @param  \App\Models\EmployeeAllocation  $employee_id
     * @return \Illuminate\Http\Response
     */
    public function unallocate(Request $request)
    {
        try {
            DB::beginTransaction();

            $unallocation = '';
            if ($request->has(['employee_id']) && sizeof(json_decode($request->get('team_ids'))) == 0) {
                $user_id = $request->get('employee_id');

                $user_team_input['team_id'] = 0;
                $user_team_input['user_id'] = $user_id;
                $unallocation = $this->user_team_repository->userUnallocation($user_team_input);
                if ($unallocation) {
                    $allocatedIds = $this->user_course_allocation->userAllAllocatedRecordIds($user_id)->toArray();
                    $delete = $this->user_course_allocation->userAllUnallocation($user_id);
                    if ($delete) {
                        $this->user_course_contents->removeAllByUserId($user_id);
                        $unallocation = $this->user_team_course_allocation_repository->userAllUnallocation($allocatedIds);
                    }
                }
            } elseif ($request->has(['employee_id', 'team_ids']) && sizeof(json_decode($request->get('team_ids'))) >= 1) {
                $user_id = $request->get('employee_id');
                foreach (json_decode($request->get('team_ids')) as $team_id) {
                    $user_team_input['team_id'] = $team_id;
                    $user_team_input['user_id'] = $user_id;
                    $unallocation = $this->user_team_repository->userUnallocation($user_team_input);
                    if ($unallocation) {
                        $userTeamCourses = $this->user_course_allocation->getUserAllocatedCourseByTeam($user_team_input);

                        foreach ($userTeamCourses as $userTeamCourse) {
                            if (sizeof($userTeamCourse->trainingUserTeamCourseAllocation) == 1) {
                                $delete = $this->user_course_allocation->userUnallocation($userTeamCourse->id);
                                if ($delete) {
                                    $delete_contents = ['user_id' => $user_id, 'course_id' => $userTeamCourse->course_id];
                                    $this->user_course_contents->removeByCourseIdAndUserId($delete_contents);
                                    $this->user_team_course_allocation_repository->userUnallocation($userTeamCourse->trainingUserTeamCourseAllocation[0]->id);
                                }
                            } elseif (sizeof($userTeamCourse->trainingUserTeamCourseAllocation) > 1) {
                                foreach ($userTeamCourse->trainingUserTeamCourseAllocation as $t) {
                                    if ($t->team_id == $team_id) {
                                        $this->user_team_course_allocation_repository->userUnallocation($t->id);
                                    }
                                }
                            } else {
                            }
                        }
                    }
                }
            }
            DB::commit();
            if ($unallocation) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


    /**
     * Update Course Allocation when team edit.
     *
     * @param  \App\Models\EmployeeAllocation  $employee_id
     * @return \Illuminate\Http\Response
     */

    public function updateCourseAllocationOnTeamEdit($teamIds)
    {
        $alreadyAllottedTeam = array();
        if (sizeof($teamIds) >= 1) {
            foreach ($teamIds as $team_id) {
                $team_users = $this->user_team_repository->getAllByTeamId($team_id);

                foreach ($team_users as $team_user) {
                    $user_id = $team_user->user_id;
                    $training_user_id = $team_user->training_user_id;
                    $team_courses = $this->team_repository->getCoursesByTeamId($team_id);

                    if (sizeof($team_courses) >= 1) {
                        foreach ($team_courses as $team_course) {
                            $inputs['created_by'] = Auth::id();
                            if($team_id == config('globals.rec_training_id')){
                                $inputs['training_user_id'] = $training_user_id;
                            }else{
                                $inputs['user_id'] = $user_id;
                            }
                            $inputs['course_id'] = $team_course->course_id;
                            /******* Checking course already added to the User  **/
                            $is_course_already_allocated = array();
                            $is_course_already_allocated = $this->user_course_allocation->checkCourseAlreadyAllocated($inputs);
                            // if(isset($is_course_already_allocated) && sizeof($is_course_already_allocated)>=1){
                            if (isset($is_course_already_allocated)) {

                                /***START**** If course already added to employee,
                                 * Checking for Course from allocating team or not.
                                 * If not add team id ageniest to the employee allocation record
                                 **/
                                // if (!empty($is_course_already_allocated['TrainingUserTeamCourseAllocation']) && sizeof($is_course_already_allocated['TrainingUserTeamCourseAllocation'])>=1) {
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
                                    $user_contents_input = ['user_id' => $user_id,'training_user_id' => $user_id, 'course_id' => $team_course->course_id];
                                    $this->user_course_contents->store($user_contents_input);
                                    $user_team_course['training_user_course_allocation_id'] = $user_course_allocation->id;
                                    $user_team_course['team_id'] = $team_id;
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
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('learningandtraining::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('learningandtraining::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    public function test()
    {
        $data['user_id'] = 2;
        $team_id = $data['team_id'] = 17;
        $userTeamCourses =  $this->user_course_allocation->getUserAllocatedCourseByTeam($data);

        foreach ($userTeamCourses as $userTeamCourse) {
            if (sizeof($userTeamCourse->trainingUserTeamCourseAllocation) == 1) {
                $delete = $this->user_course_allocation->userUnallocation($userTeamCourse->id);
                if ($delete) {
                    $unallocation = $this->user_team_course_allocation_repository->userUnallocation($userTeamCourse->trainingUserTeamCourseAllocation->id);
                }
            } elseif (sizeof($userTeamCourse->trainingUserTeamCourseAllocation) > 1) {
                foreach ($userTeamCourse->trainingUserTeamCourseAllocation as $t) {
                    if ($t->team_id == $team_id) {
                        $unallocation = $this->user_team_course_allocation_repository->userUnallocation($t->id);
                    }
                }
            } else {
            }
        }
    }
}
