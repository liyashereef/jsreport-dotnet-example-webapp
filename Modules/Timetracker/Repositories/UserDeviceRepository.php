<?php

namespace Modules\Timetracker\Repositories;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Timetracker\Models\UserDevice;

class UserDeviceRepository
{
    protected $model;

    public function __construct()
    {
        //        $this->UserDeviceRepository = $UserDeviceRepository;
        $this->model = new UserDevice();
    }


    /**
     * Fetch all device details with User details.
     * @return Response
     */

    public function getAll()
    {
        return $this->model->with('user')->get();
    }

    /**
     * get a device id by User.
     * @param  user_id
     * @return Response
     */

    public function getByUserId($user_id)
    {
        return $this->model->where('user_id', $user_id)->first();
    }

    /**
     * Store a device token for a user.
     * @param  Request $request
     * @return Response
     */

    public function store($inputs)
    {
        $device_exists = 0;
        $user_id = Auth::id();
        $device_exists = $this->model->where('user_id', '!=', $inputs['user_id'])
            ->where('device_token', $inputs['device_token'])
            ->count();
        if (isset($device_exists) && $device_exists >= 1) {
            $this->deleteByDeviceToken($inputs['device_token']);
        }
        return $this->model->updateOrCreate(['user_id' => $inputs['user_id']], $inputs);
    }

    /**
     *remove a record by device_token.
     * @param  $token
     * @return Response
     */

    public function deleteByDeviceToken($token)
    {
        return $this->model->where('device_token', $token)->delete();
    }


    /**
     * @param array $userIds
     * @param int $appId
     */
    public function getByAppSpecific($appId, $userIds)
    {
        return $this->model->whereIn('user_id', $userIds)->where('app_id', $appId)->get();
    }
}
