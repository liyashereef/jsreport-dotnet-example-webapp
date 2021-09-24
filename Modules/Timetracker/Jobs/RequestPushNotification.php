<?php

namespace Modules\Timetracker\Jobs;

use App\Services\AppId;
use App\Services\FireBase;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Admin\Models\User;
use Modules\Timetracker\Models\UserDevice;
use Modules\Timetracker\Models\PushNotificationLog;
use Illuminate\Support\Facades\Log;

class DispatchRequestPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user_ids, $dispatch_request_id, $subject;


    public function __construct($user_ids, $dispatch_request_id, $subject)
    {
        $this->user_ids = $user_ids;
        $this->dispatch_request_id = $dispatch_request_id;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('customlog')->info('------START----- PUSH');
        Log::channel('customlog')->info('----- user_ids ' . json_encode($this->user_ids) . 'dispatch_request_id ' . $this->dispatch_request_id . 'subject ' . $this->subject);

        $firebase = new FireBase();
        $msg_title = "Dispatch Request";
        $msg_body = $this->subject;
        $inputs['dispatch_request_id'] = $this->dispatch_request_id;

        $user_devices = UserDevice::whereIn('user_id', $this->user_ids)->get();
        // dd($this->user_ids);
        foreach ($user_devices as $device) {
            Log::channel('customlog')->info('------device-----  ' . $device->id);
            // $device_token = 'c3ETfYEdiZ0:APA91bHn8mEVYcYUhFbgO6enR3sknVBV4NyUqQhG2hTsKsyySRXnudhgtVVt81Mgohcx68KpAAfnGVvI1WUD8ZRSk78-a3nyA3iyIEqzqsWu3tskCObN6sRK2-jKYAlcHBwMPH4WZTge';
            // $response = $firebase->sendNotification($device_token,
            $response = $firebase->sendNotification(
                $device->device_token,
                [
                    "notification" => [
                        "title" => $msg_title,
                        "body" => $msg_body,
                        'vibrate' => 1,
                        "notification_icon" => "fcm_push_icon",
                        "sound" => "default",
                        "dispatch_request_id" => $this->dispatch_request_id,
                    ],
                    'data' => [
                        'title' => $msg_title,
                        'body' => $msg_body,
                        'vibrate' => 1,
                        "notification_icon" => "fcm_push_icon",
                        "sound" => "default",
                        "dispatch_request_id" => $this->dispatch_request_id,
                    ],
                    'priority' => 'high',
                    'restricted_package_name' => '',
                ],
                AppId::TIME_TRACKER
            );

            //For log creation in mongoDB
            $inputs['user_id'] = $device->user_id;
            $inputs['response'] = $response;
            if (isset($response) && json_decode($response)->success == 0) {
                $inputs['status'] = 0;
            } else {
                $inputs['status'] = 1;
            }
            $inputs['updated_at'] = Carbon::now();
            $inputs['created_at'] = Carbon::now();
            PushNotificationLog::create($inputs);
        }
        Log::channel('customlog')->info('------END----- PUSH');
    }
}
