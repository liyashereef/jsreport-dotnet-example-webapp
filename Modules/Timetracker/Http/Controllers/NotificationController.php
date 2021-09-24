<?php

namespace Modules\Timetracker\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Timetracker\Models\Notification;
use Modules\Timetracker\Repositories\NotificationRepository;

class NotificationController extends Controller
{

    /**
     * The NotificationRepository instance.
     *
     * @var \App\Repositories\NotificationRepository
     * @var \App\Services\HelperService
     */
    protected $notificationRepository, $helperService;

    /**
     * Create a new NotificationController instance.
     *
     * @param  \App\Repositories\NotificationRepository $NotificationRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(NotificationRepository $notificationRepository, HelperService $helperService)
    {
        $this->notificationRepository = $notificationRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('timetracker::notification.notification');
    }

    /* Function to get the notificaion messages */

    public function getNotificationMessage()
    {
        $all_notifications = $this->notificationRepository->getNotifications();
        return datatables()->of($all_notifications)->toJson();
    }

    /* Function for deleting multiple notification */

    public function read(Request $request)
    {
        try {
            \DB::beginTransaction();
            $notification_id_list = json_decode($request->get('notification_ids'));
            $read_notifications = $this->notificationRepository->readNotifications($notification_id_list);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /* Function for deleting single notification */

    public function delete(Request $request)
    {
        try {
            \DB::beginTransaction();
            $delete_notification = $this->notificationRepository->deleteNotifications($request);
            \DB::commit();
            if ($delete_notification) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /* Function for deleting multiple notification */

    public function multiDelete(Request $request)
    {
        try {
            \DB::beginTransaction();
            $notification_id_list = json_decode($request->get('notification_ids'));
            $multi_delete_notifications = $this->notificationRepository->multiDeleteNotifications($notification_id_list);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

}
