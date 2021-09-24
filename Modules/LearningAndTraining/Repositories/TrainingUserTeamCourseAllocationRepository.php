<?php

namespace Modules\LearningAndTraining\Repositories;


use Modules\LearningAndTraining\Models\Team;
use Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation;
use Modules\Admin\Models\User;
use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Models\TrainingUserTeam;
use Modules\LearningAndTraining\Models\TrainingUserTeamCourseAllocation;
class TrainingUserTeamCourseAllocationRepository
{

    protected $team, $team_course_allocation;

    /**
     * Create a new Team instance.
     *
     * @param Modules\LearningAndTraining\Models\Team $team
     */
    public function __construct(){
        $this->team = new Team();
        $this->user_course_allocation = new TrainingUserCourseAllocation();
        $this->user_team = new TrainingUserTeam();
        $this->user_team_course_allocation = new TrainingUserTeamCourseAllocation();
    }

    /**
     * Get training user_team_course_allocation lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->user_team_course_allocation->select(['id', 'team_id', 'training_user_course_allocation_id'])
            ->with('team','trainingUserCourseAllocation')
            ->get();
    }


    /**
     * Get user_team_course_allocation lookup list
     *
     * @param team_id
     * @return array
     */
    public function getTrainingUserCourseAllocationByTeamId($team_id)
    {
        return $this->user_team_course_allocation->select(['id', 'team_id', 'training_user_course_allocation_id'])
            ->where('team_id',$team_id)
            ->with('trainingUserCourseAllocation')
            ->get();
    }



    /**
     * Store Details
     *
     * @param
     * @return object
     */
    public function store($data){
        return $this->user_team_course_allocation->create($data);
    }

    public function userAllUnallocation($allocatedIds){
        return $this->user_team_course_allocation->whereIn('training_user_course_allocation_id',$allocatedIds)->delete();
    }

    public function userUnallocation($id){
        return $this->user_team_course_allocation->where('id',$id)->delete();
    }

    public function userUnallocationByTeamId($team_id){
        return $this->user_team_course_allocation->where('team_id',$team_id)->delete();
    }

    public function unAllocate($inputs){
        return $this->user_team_course_allocation->where('team_id',$inputs['team_id'])
            ->whereHas('course',function($query) {
                $query->where('trainingUserCourseAllocation','<',date('Y-m-d'));
            })->get();
    }


    public function getByUserCourseAllocationId($training_user_course_allocation_id,$team_id){
        return $this->user_team_course_allocation
            ->where('training_user_course_allocation_id',$training_user_course_allocation_id)
            ->where('team_id','!=',$team_id)
            ->get();
    }







}


?>
