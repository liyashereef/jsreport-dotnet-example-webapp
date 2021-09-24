<?php

namespace Modules\Admin\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Http\Requests\PushNotificationRoleSettingsRequest;
use Modules\Timetracker\Repositories\PushNotificationRoleSettingRepository;

class MSTDispatchPushNotificationRoleSettingsController extends Controller
{
    protected $repository;
    protected $helper_service;
    protected $user_repository;

    public function __construct(PushNotificationRoleSettingRepository $repository,
                                UserRepository $userRepository,
                                HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helper_service = $helperService;
        $this->user_repository = $userRepository;
    }

    public function index()
    {
        return view("timetracker::admin.push_notification_role_settings.index");
    }

    /**
     * Attach Role to push notification
     *
     * @param PushNotificationRoleSettingsRequest $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function store(PushNotificationRoleSettingsRequest $request)
    {
        $role = $request->input('role');

        $hasRole = $this->repository->getByRole($role);

        //check the role already exists
        if ($hasRole->isEmpty()) {
            $attachedRole = $this->repository->save([
                'role' => $role,
                'created_by' => Auth::id()
            ]);
            return response()->json([
                'success' => true,
                'result' => $attachedRole
            ]);
        }
        return $this->helper_service->returnFalseResponse();

    }

    /**
     * Remove the attached role.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $is_deleted = $this->repository->delete($id);
            \DB::commit();
            if ($is_deleted == false) {
                return response()->json($this->helper_service->returnFalseResponse());
            } else {
                return response()->json($this->helper_service->returnTrueResponse(null));
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helper_service->returnFalseResponse($e));
        }
    }

    /**
     * Get all attached roles list. response as  datatable supported format.
     * @return mixed
     * @throws \Exception
     */
    public function list()
    {
        return datatables()->of($this->repository->getAll())->toJson();
    }


    public function roleDataForAllocation()
    {
        return response()->json([
            "success" => true,
            "data" => $this->repository->filteredRolesArray()
        ]);
    }


}
