<?php


namespace Modules\LearningAndTraining\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\LearningAndTraining\Models\Team;
use Modules\LearningAndTraining\Models\TrainingUserTeam;

class TrainingUserTeamRepositories
{

    public function __construct()
    {
        $this->team = new Team();
        $this->user_team = new TrainingUserTeam();
    }

    /**
     * Get training user_team lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->user_team->select(['id', 'user_id', 'team_id'])->with('team', 'user')->get();
    }

    /**
     * Get user team list
     *
     * @param $user_id
     * @return array
     */
    public function getAllByUserId($user_id)
    {
        return $this->user_team->select(['id', 'user_id', 'team_id','created_at'])->where('user_id', $user_id)->with('team', 'team.team', 'user')->get();
    }

    /**
     * Get user team list
     *
     * @param $team_id
     * @return array
     */
    public function getAllByTeamId($team_id)
    {
        return $this->user_team->select(['id', 'user_id', 'team_id','training_user_id'])->where('team_id', $team_id)->get();
    }

    /**
     * Store Details
     *
     * @param
     * @return object
     */
    public function store($data)
    {
        return $this->user_team->create($data);
    }

    /** */

    public function checkTeamAlreadyAllocated($inputs)
    {
        if (isset($inputs['user_id'])) {
            return $this->user_team->where('user_id', $inputs['user_id'])
            ->where('team_id', $inputs['team_id'])->count();
        } else {
            return $this->user_team->where('training_user_id', $inputs['training_user_id'])
            ->where('team_id', $inputs['team_id'])->count();
        }
    }

    public function userUnallocation($inputs)
    {

            $emp_unallocation = $this->user_team->where('user_id', $inputs['user_id']);
        if ($inputs['team_id'] != 0) {
            $emp_unallocation->where('team_id', $inputs['team_id']);
        }
            $result = $emp_unallocation->delete();
            return $result;
    }

    public function removeByTeamId($team_id)
    {
        return $this->user_team->where('team_id', $team_id)->delete();
    }

    public function deleteByTeamIds($team_ids)
    {
        return $this->user_team->whereIn('team_id', $team_ids)->delete();
    }
}
