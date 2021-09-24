<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\ProjectManagement\Models\PmTaskOwner;

class TaskOwnerRepository
{

    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(PmTaskOwner $taskOwner)
    {
        $this->model = $taskOwner;
    }

    public function deleteTaskOwnersByTaskId($taskId)
    {

        return $this->model->where('task_id', $taskId)->delete();
    }

    public function saveTaskOwners($taskId, $taskOwners, $followers)
    {
        //clear existing entries against task id
        $this->deleteTaskOwnersByTaskId($taskId);

        //insert new task owners
        if (!empty($taskOwners)) {
            foreach ($taskOwners as $taskOwnerId) {
                $taskOwner = new PmTaskOwner;
                $taskOwner->task_id = $taskId;
                $taskOwner->user_id = $taskOwnerId;
                $taskOwner->type = 0;
                $taskOwner->save();
            }
        }

        //insert new followers
        if (!empty($followers)) {
            foreach ($followers as $followerId) {
                $follower = new PmTaskOwner;
                $follower->task_id = $taskId;
                $follower->user_id = $followerId;
                $follower->type = 1;
                $follower->save();
            }
        }
        return true;
    }

    public function getAllocatedTaskIds($userIds, $type = '')
    {
        return $this->model->whereIn('user_id', $userIds)->pluck('task_id')->toArray();
    }

    public function fetchTaskOwnerVsFollowerName($taskId, $type = false)
    {
        $name = '';
        $objects = $this->model->with(['user'])->where('task_id', $taskId)->where('type', $type)->get();
        if (!empty($objects)) {
            foreach ($objects as $object) {
                $name .= $object->user->full_name . ",";
            }
        }
        return $name;
    }

}
