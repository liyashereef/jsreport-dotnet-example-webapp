<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Services\FireBase;
use Modules\Timetracker\Models\DispatchUserDevice;
use Modules\Timetracker\Models\PushNotificationLog;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class PushNotificationRepository
{
    public function __construct()
    {
    }

    public function sendPushNotification($userIds, $request_id, $request_type, $title, $subject)
    {
        $firebase = new FireBase();
        $deviceTokenIds = array();
        $each_row = [];
        $msg_title = $title;
        $msg_body = $subject;
        $inputs['request_id'] = $request_id;
        $inputs['request_type'] = $request_type;

        $user_devices = DispatchUserDevice::whereIn('user_id', $userIds)
            ->select('id', 'device_token', 'user_id')
            ->get();
        $sound = "default";
        if ($request_type == PUSH_CGL_MEET) {
            $sound = "cglmeet";
        }
        foreach ($user_devices as $device) {
            $response = $firebase->pushToUser(
                $device->device_token,
                [
                    "notification" => [
                        "title" => $msg_title,
                        "body" => $msg_body,
                        'vibrate' => true,
                        "notification_icon" => "fcm_push_icon",
                        "sound" => $sound,
                        "request_id" => $request_id,
                        "request_type" => $request_type,
                        "received_time" => date("h:i A"),
                        "received_date" => date("l, M d, Y"),
                    ],
                    'data' => [
                        'title' => $msg_title,
                        'body' => $msg_body,
                        'vibrate' => true,
                        "notification_icon" => "fcm_push_icon",
                        "sound" => $sound,
                        "request_id" => $request_id,
                        "request_type" => $request_type,
                        "received_time" => date("h:i A"),
                        "received_date" => date("l, M d, Y"),
                    ],
                    'priority' => 'high',
                    'restricted_package_name' => '',
                ]
            );

            //For log creation in mongoDB
            $inputs['title'] = $msg_title;
            $inputs['message'] = $msg_body;
            $inputs['user_id'] = $device->user_id;
            $inputs['response'] = $response;
            if (isset($response) && json_decode($response)->success == 0) {
                if (isset($response) && json_decode($response)->results[0]->error == 'NotRegistered') {
                    $each_row =  $device->id;
                    array_push($deviceTokenIds, $each_row);
                }
                $inputs['status'] = 0;
            } else {
                $inputs['status'] = 1;
            }
            $inputs['updated_at'] = Carbon::now();
            $inputs['created_at'] = Carbon::now();
            PushNotificationLog::create($inputs);
        }
        if (isset($deviceTokenIds) && !empty($deviceTokenIds)) {
            return $this->updateDeviceToken($deviceTokenIds);
        } else {
            return true;
        }
    }

    public function getAllPushNotifications($user_id, $request)
    {
        $paginationResult = intval($request['result']);
        $currentPage  = intval($request['pageNo']);
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        return PushNotificationLog::where('user_id', $user_id)
            ->where('request_type', '!=', PUSH_MST)
            ->select('request_id', 'request_type', 'title', 'message', 'user_id', 'status', 'is_read', 'response', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($paginationResult);
    }

    public function updatePushNotificationReadFlag($request)
    {
        //For message read  updation in mongoDB
        $id = $request['_id'];
        $readFlag =  intval($request['is_read']);
        return  PushNotificationLog::where('_id', $id)->update(['is_read' => $readFlag]);
    }

    public function getUnreadNotifications($userid)
    {
        return PushNotificationLog::where('user_id', $userid)->where('request_type', '!=', PUSH_MST)->where('is_read', null)->get()->count();
    }

    public function updateDeviceToken($deviceTokenIds)
    {
        return  DispatchUserDevice::whereIn('id', $deviceTokenIds)->delete();
    }
}
