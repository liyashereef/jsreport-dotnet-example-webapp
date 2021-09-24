<?php

namespace Modules\LearningAndTraining\Repositories;


use Modules\LearningAndTraining\Models\Team;
use Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation;
use Modules\Admin\Models\User;
use Modules\LearningAndTraining\Models\RegisterCourse;
use Modules\LearningAndTraining\Repositories\TeamRepository;

class TrainingTeamCourseAllocationRepository
{

    protected $team, $team_course_allocation;

    /**
     * Create a new Team instance.
     *
     * @param Modules\LearningAndTraining\Models\Team $team
     */
    public function __construct(Team $team,TeamRepository $team_repo,TrainingTeamCourseAllocation $team_course_allocation)
    {
        $this->team = $team;
        $this->team_repo = $team_repo;
        $this->team_course_allocation = $team_course_allocation;
        $this->user_course_allocation = new TrainingUserCourseAllocationRepository();
    }

    /**
     * Get training team_course_allocation lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->team_course_allocation->select(['id', 'team_id', 'course_id', 'mandatory', 'recommended'])->with('team','training_course')->get();
    }


    /**
     * Get training course lookup list
     *
     * @param team_id
     * @return array
     */
    public function getTrainingCourseAllocationByTeamId($team_id)
    {
        return $this->team_course_allocation->select(['id', 'team_id', 'course_id', 'mandatory', 'recommended'])->where('team_id',$team_id)->get();
    }
    /**
     * Get training course lookup list
     *
     * @param team_id
     * @return array
     */
    public function getTrainingCourseAllocationByTeamParentId($team_id)
    {
        return $this->team_course_allocation->select(['id', 'team_id', 'course_id', 'mandatory', 'recommended'])->where('team_id',$team_id)->get();
    }

    /**
     * Storing parent team courses to child team.
     *
     * @param team_id,parent_team_id
     * @return array
     */

    public function addParentTeamCourseToSubTeam($request){
// ---------------- START ---- storing parent team courses to child team. ------------------
        if($request['parent_team_id'] !=0){
            $parent_courses = $this->getTrainingCourseAllocationByTeamId($request['parent_team_id'])->toArray();

            if(sizeof($parent_courses)>=1){

                foreach ($parent_courses as $parent_course){

                    $parent_course_inputs['course_id'] = $parent_course['course_id'];
                    $parent_course_inputs['mandatory'] = $parent_course['mandatory'];
                    $parent_course_inputs['recommended'] = $parent_course['recommended'];
                    $parent_course_inputs['parent_team_id'] = $parent_course['team_id'];
                    $parent_course_inputs['team_id'] = $request['team_id'];

                    $count = $this->getCourseTeamAllocationCount($request['team_id'],$parent_course['course_id']);
                    if($count == 0){
                        $this->store($parent_course_inputs);
                    }
                    $parent_course_inputs = array();
                }
            }
            return 1;

        }else{
            return 0;
        }
// ---------------- END ---- storing parent team courses to child team. ------------------
    }

    public function updateParentTeamCourseToSubTeam($team_id){

        $parent_courses = $this->getTrainingCourseAllocationByTeamId($team_id)->toArray();
        $sub_team_list = $this->team_repo->getSubTeamsByParentId($team_id)->toArray();

        if(sizeof($parent_courses)>=1 && sizeof($sub_team_list)>=1) {

            foreach ($parent_courses as $parent_course) {

                $parent_course_inputs['course_id'] = $parent_course['course_id'];
                $parent_course_inputs['mandatory'] = $parent_course['mandatory'];
                $parent_course_inputs['recommended'] = $parent_course['recommended'];
                $parent_course_inputs['parent_team_id'] = $parent_course['team_id'];

                foreach ($sub_team_list as $sub_team){
                    $parent_course_inputs['team_id'] = $sub_team['id'];
                    $count = $this->getCourseTeamAllocationCount($sub_team['id'],$parent_course['course_id']);
                    if($count == 0){
                        $this->store($parent_course_inputs);
                    }
                }
                $parent_course_inputs = array();
            }
        }
    }

    /**
     * Store Details
     *
     * @param
     * @return object
     */
    public function store($data)
    {
        $registerCourse = $this->team_course_allocation->create($data);
        return $registerCourse;
    }

    /**
     * get course allocation count in a team
     *
     * @param $team_id , $course_id
     * @return count
     */
    public function getCourseTeamAllocationCount($team_id,$course_id){
        return $this->team_course_allocation->where('team_id',$team_id)->where('course_id',$course_id)->count();
    }

    /**
     * Get training course allocation list of a parent team
     *
     * @param team_id
     * @return array
     */
    public function getTeamCourses($team_id)
    {
        return $this->team_course_allocation->select(['id','course_id', 'mandatory', 'recommended'])
            ->where('team_id',$team_id)
            ->where('parent_team_id',0)
            ->get();
    }
    /**
     * remove allocated training course
     *
     * @param team_id
     * @return array
     */
    public function deleteTeamCourses($delete){

        $teamCourse = $this->team_course_allocation->where('team_id',$delete['team_id'])
            ->whereIn('course_id',$delete['course_ids_diff'])
            ->delete();

//--------------- For Parent Team-- Removing courses from Parent Team & Sub Team -----------
        if($delete['parent_team_id'] == 0){
            $this->team_course_allocation->where('parent_team_id',$delete['team_id'])
                ->whereIn('course_id',$delete['course_ids_diff'])
                ->delete();
            }

        return $teamCourse;
    }

    public function deleteTeamCoursesByCourseId($course_id){
        return $this->team_course_allocation
            ->where('course_id', $course_id)
            ->delete();
    }

        public function deletePatentTeamCourseFromSubTeam($delete){

        /**START* Old Code */
//         return $this->team_course_allocation->where('parent_team_id',$delete['old_parent_team_id'])
//            ->where('team_id',$delete['team_id'])
//            ->delete();
        /**END* Old Code */

        $team_parent_courses = $this->team_course_allocation->where('parent_team_id',$delete['old_parent_team_id'])
            ->where('team_id',$delete['team_id'])
            ->get();
        if(isset($team_parent_courses) && sizeof($team_parent_courses) >=1){
            $inputs['team_id'] = $delete['team_id'];
            $inputs['old_parent_team_id'] = $delete['old_parent_team_id'];
            foreach($team_parent_courses as $team_parent_course){
                $inputs['course_id'] = $team_parent_course->course_id;
                $this->user_course_allocation->unAllocateByCourseIdNadTeamId($inputs);
            }

            return $this->team_course_allocation->where('parent_team_id',$delete['old_parent_team_id'])
            ->where('team_id',$delete['team_id'])
            ->delete();
        }

    }






}


?>