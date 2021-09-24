<?php

namespace Modules\Management\Http\Controllers;

use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\UserCertificate;
use Modules\Admin\Models\User;
use Modules\Management\Repositories\UserViewRepository;
use Modules\Admin\Models\SecurityClearanceUser;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Management\Http\Requests\UserTabRequest;
use App\Services\HelperService;
use Modules\Management\Http\Requests\UserProfileTabRequest;
use Modules\Management\Http\Requests\SecurityClearanceRequest;
use Modules\Management\Http\Requests\UserCertificatesRequest;
use Modules\Management\Http\Requests\UserSkillRequest;

class UserViewController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function __construct(
        HelperService $helperService,
        UserViewRepository $userViewRepository,
        UserRepository $userRepository,
        UserCertificate $certificate
    ) {
        $this->userRepository = $userRepository;
        $this->userCertificate = $certificate;
        $this->userViewRepository=$userViewRepository;
        $this->helperService = $helperService;
    }

/**
     * Show the specified resource.
     * @return Response
     */

    public function getList()
    {
        $userList = $this->userViewRepository->employeeLookUps();
        return view('management::user-list', compact('userList'));
    }

        /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function getListView(Request $request)
    {

        $employeenameId = request('employeename');
        $active=request('status_id');
        if ($active==-1) {
            $active=null;
        }
        return datatables()->of($this->userViewRepository->getUserTableList($active, $employeenameId))->addIndexColumn()->toJson();
    }

     /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function userTabStore(UserTabRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->userViewRepository->userTabStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function expenseTabStore(Request $request, $id)
    {
        return $this->userViewRepository->expenseTabStore($request, $id);
    }


    public function userCertificateStore(UserCertificatesRequest $request, $id)
    {

        return $this->userViewRepository->userCertificateStore($request, $id);
    }

    public function userSecurityClearanceStore(SecurityClearanceRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->userViewRepository->userSecurityClearanceStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function userSkillStore(UserSkillRequest $request, $id)
    {

        return $this->userViewRepository->userSkillStore($request, $id);
    }


    public function profileTabStore(UserProfileTabRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->userViewRepository->profileTabStore($request, $id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function getDetailView($id)
    {
        $rolePermission=true;
        $userDetails = $this->userRepository->getUserDetails($id);
        $page_variables_arr = $this->userRepository->userIndex();
        $roles = $page_variables_arr['roles'];
        $work_types = $page_variables_arr['work_types'];
        $positions = $page_variables_arr['positions'];
        $posId=User::with('employee.employeePosition')->where('id', $id)->get();
        $approversList = $this->userRepository->getUserList(true, null, null, ['super_admin'], false, false)->sortBy('full_name')->pluck('full_name', 'id')->toArray();
        $security_clearances = $page_variables_arr['security_clearances'];
        $certificates = $page_variables_arr['certificates'];
        $user_certificate = UserCertificate::with('certificateMaster')->where('user_id', $id)->select('certificate_id', 'expires_on')->distinct('certificate_id')->get();

        $securityDataLookup=SecurityClearanceUser::with('securityClearanceLookups')->where('user_id', $id)->get();

        $viewExpenseId= DB::table('expense_allowable_for_users')->where('user_id', $id)->pluck('max_allowable_expense');
        $viewExpense = str_replace(array('[',']','"'), '', $viewExpenseId);

        $role=$this->userRepository->getUserTableList()->where('id', $id)->pluck('roles');
        $viewRole = str_replace(array('[',']','"'), '', $role);
        if ($viewRole=='Client') {
            $rolePermission=false;
        }
        $viewId= DB::table('expense_allowable_for_users')->where('user_id', $id)->pluck('reporting_to_id');
        $viewAprovId = str_replace(array('[',']','"'), '', $viewId);
        $approList = $this->userRepository->getUserList(true, null, null, ['super_admin'], false, false)
        ->where('id', $viewAprovId)->pluck('full_name', 'id')->toArray();

        $viewList = str_replace(array('[',']','"'), '', $approList);
        $userId=$id;
        $user_skills = $page_variables_arr['user_skills'];
        return view('management::user-detail-view', compact(
            'userDetails',
            'roles',
            'work_types',
            'positions',
            'posId',
            'approversList',
            'security_clearances',
            'certificates',
            'rolePermission',
            'userId',
            'securityDataLookup',
            'viewList',
            'viewExpense',
            'viewRole',
            'user_certificate',
            'user_skills'
        ));
    }
}
