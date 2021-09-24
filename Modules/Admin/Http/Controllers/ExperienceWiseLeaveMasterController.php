<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\ExperienceWiseLeaveMasterRepository;
use App\Services\HelperService;
use Modules\Admin\Models\TimeOffRequestTypeLookup;
use Modules\Admin\Models\TimeOffRequestTypeSetting;
use Modules\Admin\Models\TimeOffRolesbased;
use Spatie\Permission\Models\Role;

class ExperienceWiseLeaveMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(TimeOffRolesbased $timeOffRolesbased, TimeOffRequestTypeSetting $experienceWiseLeaveMaster, TimeOffRequestTypeLookup $timeOffRequestTypeLookup, ExperienceWiseLeaveMasterRepository $experienceWiseLeaveMasterRepository, HelperService $helperService)
    {
        $this->repository = $experienceWiseLeaveMasterRepository;
        $this->timeOffRequestTypeLookup = $timeOffRequestTypeLookup;
        $this->helperService = $helperService;
        $this->model = $experienceWiseLeaveMaster;
        $this->timeOffRolesbased = $timeOffRolesbased;
    }

    public function index()
    {
        $roles = ['super_admin', 'admin', 'client'];
        $rolesList = Role::select(['id', 'name'])->whereNotIn('name', $roles)->orderBy('name')->get();
        $months = ['1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
        $timeOffRequestType = $this->timeOffRequestTypeLookup->select(['id', 'request_type'])->get();
        return view('admin::masters.experience-wise-leave', compact('timeOffRequestType', 'months', 'rolesList'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_experience' => 'bail|required|numeric',
            'no_of_leaves' => 'bail|required|numeric|not_in:0',
            'time_off_request_type_id' => 'bail|required',
            // 'accrual_day' => 'bail|required|numeric',
            // 'accrual_month' => 'bail|required|numeric',
            'reset_month'  => 'bail|required|numeric',
            'reset_day'  => 'bail|required|numeric',
            'carry_forward_percentage' => 'bail|required|numeric',
            'carry_forward_expires_in_month' => 'bail|required|numeric',
            'encashment_percentage' => 'bail|required|numeric',
        ], [
            'min_experience.required' => 'Greaterthan is required.',
            'no_of_leaves.required' => 'No of Leave is required.',
            'no_of_leaves.not_in' => 'No of Leave is must be greater than zero.',
            'time_off_request_type_id.required' => 'Request type is required.',
            // 'accrual_day' => "Accrual Day is required.",
            // 'accrual_month' => "Accrual Month is required.",
            'reset_day'  => "Reset Day is required.",
            'reset_month' => "Reset Month is required.",
            'carry_forward_percentage' => "Carry forward Percentage is required.",
            'carry_forward.boolean' => "Carry forward must be 1 or 0.",
            'carry_forward_expires_in_month' => "Expires in Month is required.",
            'encashment_percentage' => "Encashment Percentage is required.",

        ]);

        $inputs = $request->all();
        $inputs['active'] = isset($request->active) ? 1 : 0;

        try {
            \DB::beginTransaction();

            $timeoffData = array(
                'min_experience' => $request['min_experience'],
                'no_of_leaves' => $request['no_of_leaves'],
                'time_off_request_type_id' => $request['time_off_request_type_id'],
                'reset_term' => '1',
                'reset_day' => $request['reset_day'],
                'reset_month' => $request['reset_month'],
                'carry_forward' => isset($request->carry_forward_percentage) ? 1 : 0,
                'carry_forward_percentage' => $request['carry_forward_percentage'],
                'carry_forward_expires_in_month' => $request['carry_forward_expires_in_month'],
                'encashment_percentage' => $request['encashment_percentage'],
                'active' => isset($request->active) ? 1 : 0
            );
            if (!isset($request['id'])) {
                $timeoffData['created_by'] = Auth::user()->id;
            }
            $timeoffData['updated_by'] = Auth::user()->id;
            $storeTimeoffSettings = TimeOffRequestTypeSetting::updateOrCreate(array('id' => $request['id']), $timeoffData);

            if ($inputs['id'] != null) {
                TimeOffRolesbased::where("timeoff_request_type_setting_id", $inputs['id'])->delete();
            }

            if ($request['role_id'] != null) {
                foreach ($request['role_id'] as $key => $rolesID) {
                    $dataArr = array(
                        'timeoff_request_type_setting_id' => $storeTimeoffSettings->id,
                        'role_id' => $request['role_id'][$key],
                    );
                    $dataArr['role_exception'] = isset($request->role_exception) ? 1 : 0;
                    if (!isset($request['id'])) {
                        $dataArr['created_by'] = Auth::user()->id;
                    }
                    $dataArr['updated_by'] = Auth::user()->id;
                    TimeOffRolesbased::create($dataArr);
                }
            } else {
                $dataArr = array(
                    'timeoff_request_type_setting_id' => $storeTimeoffSettings->id,
                );
                $dataArr['role_exception'] = isset($request->role_exception) ? 1 : 0;
                if (!isset($request['id'])) {
                    $dataArr['created_by'] = Auth::user()->id;
                }
                $dataArr['updated_by'] = Auth::user()->id;
                TimeOffRolesbased::create($dataArr);
            }

            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            TimeOffRolesbased::where("timeoff_request_type_setting_id", $id)->delete();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
