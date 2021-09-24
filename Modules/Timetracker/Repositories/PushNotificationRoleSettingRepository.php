<?php

namespace Modules\Timetracker\Repositories;


use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Models\PushNotificationRoleSetting;

class PushNotificationRoleSettingRepository
{
    protected $model;
    protected $user_repository;

    public function __construct()
    {
        $this->model = new PushNotificationRoleSetting();
        $this->user_repository = new UserRepository;
    }

    public function getAll()
    {
        return $this->model->all()->load(['user','push_notification_type']);
    }

    public function getByUserId($userId)
    {
        return $this->model->where('created_by', '=', $userId)->get();
    }

    public function getByRole($role)
    {
        return $this->model->where('role', '=', $role)->get();
    }

    public function getByPushNotificationType($type)
    {
        return $this->model->where('push_notification_type_id', '=',$type )->get();
    }
    public function getPushNotificationRolesArray($type){
        return $this->getByPushNotificationType($type)->pluck('role')->toArray();
    }

    public function save($data)
    {
        $result = $this->model->create($data);
        return $result->fresh();

    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getDistinctRolesAllocated()
    {
        return $this->model->distinct('role')->pluck('role')->toArray();
    }

    public function filteredRolesArray()
    {
        $filtered_roles =[];
        $roles_list = $this->user_repository->getRoleLookup(null,null);
        $allocated_roles = $this->getDistinctRolesAllocated();

        foreach ($roles_list as $key => $value){
            if(!in_array($key,$allocated_roles)){
                $filtered_roles[$key] = $value;
            }
        }
        return $filtered_roles;
    }


}
