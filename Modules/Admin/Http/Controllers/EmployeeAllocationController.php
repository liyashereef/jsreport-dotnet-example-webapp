<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Models\Employee;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Modules\Admin\Repositories\UserRepository;

class EmployeeAllocationController extends Controller
{

    protected $user_repository;
    protected $employee_allocation;
    protected $helperService;
    protected $rolesAndPermissionRepository;

    public function __construct(RolesAndPermissionRepository $rolesAndPermissionRepository)
    {
        $this->user_repository = new UserRepository();
        $this->employee_allocation = new EmployeeAllocationRepository();
        $this->helperService = new HelperService();
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role_list = $this->user_repository->getRoleLookup(null, ['super_admin', 'admin']);
        $supervisor_list = [];
        return view('admin::user.allocation', compact('supervisor_list', 'role_list'));
    }

    /**
     * Fetch user list
     *
     * @param type $role
     */
    public function getUserLookup($role = null)
    {
        $lookup_arr = array();
        if ($role != null) {
            $lookup_arr = $this->user_repository->getUserLookup(
                [$role],
                ['super_admin', 'admin'],
                true,
                false,
                null,
                false,
                false
            );
        }
        return $lookup_arr;
    }

    /**
     * List of users that can be allocated
     * @param type $role
     * @return type
     */
    public function getAllocationList($role = null, $supervisor_id = null)
    {
        $list_data = $this->employee_allocation->getUserAllocationList($role, $supervisor_id);
        return datatables()->of($list_data)->toJson();
    }

    /**
     * Store the allocated resource.
     *
     * @param  \App\Models\EmployeeAllocation  $supervisor_id
     * @return \Illuminate\Http\Response
     */
    public function allocate(Request $request)
    {
        try {
            \DB::beginTransaction();
            $employee_id_list = json_decode($request->get('employee_ids'));
            $supervisor_id = $request->get('supervisor_id');
            $allocation = $this->employee_allocation->userAllocation($employee_id_list, $supervisor_id);
            \DB::commit();
            if ($allocation) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the allocated resource from storage.
     *
     * @param  \App\Models\EmployeeAllocation  $employee_id
     * @return \Illuminate\Http\Response
     */
    public function unallocate(Request $request)
    {
        try {
            \DB::beginTransaction();
            $employee_id = $request->get('employee_id');
            $supervisor_id = $request->get('supervisor_id');
            $unallocation = $this->employee_allocation->userUnallocation($employee_id, $supervisor_id);
            \DB::commit();
            if ($unallocation) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
