<?php

namespace Modules\LearningAndTraining\Http\Controllers\Admin;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\LearningAndTraining\Http\Requests\TeamRequest;
use Modules\LearningAndTraining\Repositories\TeamRepository;
use Modules\LearningAndTraining\Repositories\TrainingTeamCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserTeamCourseAllocationRepository;

class TeamController extends Controller
{

    public function __construct(
        TeamRepository $team_repository,
        TrainingTeamCourseAllocationRepository $course_team_allocation_repository,
        TrainingCourseRepository $course_repository

    ) {
        $this->team_repository = $team_repository;
        $this->course_team_allocation_repository = $course_team_allocation_repository;
        $this->course_repository = $course_repository;
        $this->helper_service = new HelperService();
        $this->emp_allocation_ctrl = new EmployeeAllocationController();
        $this->user_allocation_repo = new TrainingUserCourseAllocationRepository();
        $this->user_team_course_allocation_repo = new TrainingUserTeamCourseAllocationRepository();
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('learningandtraining::admin.team.list');
    }

    /**
     * Get table list
     */
    public function getTableList()
    {
        return datatables()->of($this->team_repository->getTableList())->toJson();
    }
    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->team_repository->get($id));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id = null)
    {
        $mandatory_course_array = array();
        $recommended_course_array = array();
        $team_details = null;

        $teams = $this->team_repository->getParentTeams();
        $courses = $this->course_repository->getAllForTeamListing();

        if ($id != null) {

            $team_details = $this->team_repository->getById($id);
            $team_courses = $this->course_team_allocation_repository->getTeamCourses($id);
            if (sizeof($team_courses) >= 1) {
                foreach ($team_courses as $course) {

                    if ($course->recommended != 0) {
                        array_push($recommended_course_array, $course->course_id);
                    } elseif ($course->mandatory != 0) {
                        array_push($mandatory_course_array, $course->course_id);
                    }
                }
            }
        }
        return view('learningandtraining::admin.team.form', compact('teams', 'courses', 'team_details', 'mandatory_course_array', 'recommended_course_array', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(TeamRequest $request)
    {

        try {

            DB::beginTransaction();

            if ($request->get('team_id') == null) {
                $team_register = $this->team_repository->create($request);
                if ($team_register) {
                    $request->request->add(['edit_flag' => 0]);
                    $request->request->add(['team_id' => $team_register->id]);
                    $request->request->add(['parent_team_id' => $request->get('parent_team_id')]);

                    if (!$request->has('mandatory_course')) {
                        $request->request->add(['mandatory_course' => array()]);
                    }
                    if (!$request->has('recommended_course')) {
                        $request->request->add(['recommended_course' => array()]);
                    }

                    $this->storeTeamCourseAllocation($request->all());
                } else { }
            } else {
                $team_details = $this->team_repository->getById($request->get('team_id'));

                $request->request->add(['old_parent_team_id' => $team_details->parent_team_id]);
                $request->request->add(['edit_flag' => 1]);

                if (!$request->has('mandatory_course')) {
                    $request->request->add(['mandatory_course' => array()]);
                }
                if (!$request->has('recommended_course')) {
                    $request->request->add(['recommended_course' => array()]);
                }
                $allocation_return = $this->storeTeamCourseAllocation($request->all());

                if ($allocation_return) {

                    $teamIds = array();
                    array_push($teamIds, $request->get('team_id'));
                    if ($team_details->parent_team_id == 0) {
                        $team_subs = $this->team_repository->getSubTeamsByParentId($request->get('team_id'));
                        if (sizeof($team_subs) >= 1) {
                            foreach ($team_subs as $t) {
                                array_push($teamIds, $t->id);
                            }
                        }
                    }

                    $this->emp_allocation_ctrl->updateCourseAllocationOnTeamEdit($teamIds);
                }

                $team_register = $this->team_repository->update($request);
            }
            DB::commit();
            return response()->json($this->helper_service->returnTrueResponse());
            //                return response()->json($this->helper_service->returnFalseResponse());


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
    }
    //Allocation from allocation page.
    public function storeTeamCourseAllocation($request)
    {

        $inputs = array();
        $mandatory_courses = array();
        $recommended_courses = array();
        $parent_courses = array();
        $team_allocated_mandatory_course_ids = array();
        $team_allocated_recommended_course_ids = array();
        $mandatory_course_ids_diff = array();
        $recommended_course_ids_diff = array();
        $count = 0;


        $inputs['team_id'] = $request['team_id'];

        if ($request['edit_flag'] == 0) {  // For Creating new Team

            // ---------------- START ---- storing parent team courses to child team. ------------------
            //            if($request['parent_team_id'] !=0){
            //                $inputs['parent_team_id'] = $request['parent_team_id'];
            //                $this->course_team_allocation_repository->addParentTeamCourseToSubTeam($inputs);
            //            }
            //            if($request['parent_team_id'] !=0){
            //                $parent_courses = $this->course_team_allocation_repository->getTrainingCourseAllocationByTeamId($request['parent_team_id'])->toArray();
            //            }

            //            if(sizeof($parent_courses)>=1){
            //
            //                foreach ($parent_courses as $parent_course){
            //
            //                    $parent_course_inputs['course_id'] = $parent_course['course_id'];
            //                    $parent_course_inputs['mandatory'] = $parent_course['mandatory'];
            //                    $parent_course_inputs['recommended'] = $parent_course['recommended'];
            //                    $parent_course_inputs['parent_team_id'] = $parent_course['team_id'];
            //                    $parent_course_inputs['team_id'] = $inputs['team_id'];
            //
            //                    $count = $this->course_team_allocation_repository->getCourseTeamAllocationCount($inputs['team_id'],$parent_course['course_id']);
            //                    if($count == 0){
            //                        $this->course_team_allocation_repository->store($parent_course_inputs);
            //                    }
            //                    $parent_course_inputs = array();
            //                }
            //            }
            // ---------------- END ---- storing parent team courses to child team. ------------------

        } else { // For Edit Team

            $team_courses = $this->course_team_allocation_repository->getTeamCourses($inputs['team_id']);
            // ---------------- START ---- On Edit --  removing non existing course ------------------
            foreach ($team_courses as $team_course) {
                if ($team_course->mandatory == 1) {
                    array_push($team_allocated_mandatory_course_ids, $team_course->course_id);
                }
                if ($team_course->recommended == 1) {
                    array_push($team_allocated_recommended_course_ids, $team_course->course_id);
                }
            }

            if (sizeof($team_allocated_mandatory_course_ids) >= 1 && isset($request['mandatory_course']) && sizeof($request['mandatory_course']) >= 1) {
                $mandatory_course_ids_diff = array_diff($team_allocated_mandatory_course_ids, $request['mandatory_course']);
            } elseif (sizeof($team_allocated_mandatory_course_ids) >= 1 && isset($request['mandatory_course']) && sizeof($request['mandatory_course']) == 0) {
                $mandatory_course_ids_diff = $team_allocated_mandatory_course_ids;
            } else {
                $mandatory_course_ids_diff = array();
            }

            if (sizeof($team_allocated_recommended_course_ids) >= 1 && isset($request['recommended_course']) && sizeof($request['recommended_course']) >= 1) {
                $recommended_course_ids_diff = array_diff($team_allocated_recommended_course_ids, $request['recommended_course']);
            } elseif (sizeof($team_allocated_recommended_course_ids) >= 1 && isset($request['recommended_course']) && sizeof($request['recommended_course']) == 0) {
                $recommended_course_ids_diff = $team_allocated_recommended_course_ids;
            } else {
                $recommended_course_ids_diff = array();
            }

            //--------------- For Sub Team removing course from Sub team only -----------
            $delete['course_ids_diff'] = [];
            if (sizeof($mandatory_course_ids_diff) >= 1 || sizeof($recommended_course_ids_diff) >= 1) {
                $delete['course_ids_diff'] = array_merge($mandatory_course_ids_diff, $recommended_course_ids_diff);
                $delete['team_id'] = $inputs['team_id'];
                $delete['parent_team_id'] = $request['parent_team_id'];
                $team_courses_removal = $this->course_team_allocation_repository->deleteTeamCourses($delete);
                if ($team_courses_removal) {
                    /***
                     * removing employee allocation courses*
                     */

                    $this->user_allocation_repo->unAllocateByTeamIdAndCourseId($delete);
                }
            }
            // ---------------- END ---- On Edit --  removing non existing course ------------------

            if (isset($request['parent_team_id']) && $request['parent_team_id'] != $request['old_parent_team_id']) {
                $delete_parent['parent_team_id'] = $request['parent_team_id'];
                $delete_parent['old_parent_team_id'] = $request['old_parent_team_id'];
                $delete_parent['team_id'] = $inputs['team_id'];
                $this->course_team_allocation_repository->deletePatentTeamCourseFromSubTeam($delete_parent);
            }
        }
        // ---------------- START ---- storing parent team courses to child team. ------------------
        if ($request['parent_team_id'] != 0) {
            $parent_inputs['team_id'] = $inputs['team_id'];
            $parent_inputs['parent_team_id'] = $request['parent_team_id'];
            $this->course_team_allocation_repository->addParentTeamCourseToSubTeam($parent_inputs);
        }
        // ---------------- END ---- storing parent team courses to child team. ------------------

        if ($request['parent_team_id'] == 0) {
            $user_course_unallocation = [];
            if (isset($delete['course_ids_diff']) && sizeof($delete['course_ids_diff'])) {
                $user_course_unallocation['course_ids_diff'] = $delete['course_ids_diff'];
                $user_course_unallocation['team_id'] = $inputs['team_id'];

                $this->unAllocateParentTeamCourseFromSubTeam($user_course_unallocation);
            }
        }

        // ---------------- START ---- On Create & Edit --  removing non existing course ------------------
        $arr_courses['mandatory_course'] = $request['mandatory_course'];
        $arr_courses['recommended_course'] = $request['recommended_course'];

        foreach ($arr_courses as $course_type => $course) {

            if ($course_type == 'mandatory_course') {
                $inputs['recommended'] = 0;
                $inputs['mandatory'] = 1;
            } else {
                $inputs['recommended'] = 1;
                $inputs['mandatory'] = 0;
            }
            foreach ($course as $c) {
                $inputs['course_id'] = $c;
                $count = $this->course_team_allocation_repository->getCourseTeamAllocationCount($inputs['team_id'], $inputs['course_id']);
                if ($count == 0) {
                    $this->course_team_allocation_repository->store($inputs);
                }
            }
        }
        if ($request['edit_flag'] != 0) {
            $this->course_team_allocation_repository->updateParentTeamCourseToSubTeam($inputs['team_id']);
        }
        return 1;
    }


    public function unAllocateParentTeamCourseFromSubTeam($inputs)
    {

        $team_subs = $this->team_repository->getSubTeamsByParentId($inputs['team_id']);
        if (sizeof($team_subs) >= 1) {
            foreach ($team_subs as $t) {
                // array_push($teamIds, $t->id);
                foreach ($inputs['course_ids_diff'] as $key => $course_id) {

                    $data['course_id'] = $course_id;
                    $data['team_id'] = $t->id;
                    $this->user_allocation_repo->unAllocateByCourseIdNadTeamId($data);
                }
            }
        }
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
    { }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();

            $teamIds = array();
            $teamIds = $this->team_repository->getSubTeamIdsByParentId($id)->toArray();
            array_push($teamIds, $id);

            $delete = $this->team_repository->delete($teamIds, $id);

            if ($delete) {
                $this->removeAllAllocation($teamIds);
            }

            DB::commit();
            if ($delete == false) {
                return response()->json($this->helper_service->returnFalseResponse());
            } else {
                return response()->json($this->helper_service->returnTrueResponse());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function getAllWithSubTeam()
    {
        return $this->team_repository->getAllWithSubTeam();
    }

    public function getListByCourse($courseId)
    {
        return datatables()->of($this->team_repository->getListByCourse($courseId))->toJson();
    }

    public function removeAllAllocation($teamIds)
    {
        return $this->user_allocation_repo->unAllocateByTeamId($teamIds);
    }
}
