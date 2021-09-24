<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use App\User;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\KpiGroup;
use Modules\Admin\Repositories\KpiGroupEmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;

class KpiGroupEmployeeAllocationController extends Controller
{
    protected $user_repository;
    protected $repository;

    public function __construct(
        HelperService $helperService,
        UserRepository $userRepository,
        KpiGroupEmployeeAllocationRepository $repository
    ) {
        $this->helperService = $helperService;
        $this->user_repository = $userRepository;
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $kpi_group_list = KpiGroup::all()->pluck('name', 'id');
        return view('admin::kpi.employee-kpi-group-allocation', compact('kpi_group_list'));
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
        $list_data = $this->repository->tmpGetUsersWithGroupInfo();
        return datatables()->of($list_data)->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //Validation
        $request->validate([
            'kpi_group_id' => 'required|integer',
            'user_ids' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $employee_id_list = json_decode($request->input('user_ids'));
            $group_id = $request->input('kpi_group_id');
            $allocation = $this->repository->allocateEmployee($employee_id_list, $group_id, $request);
            DB::commit();

            if ($allocation) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function unallocate(Request $request)
    {
        try {
            DB::beginTransaction();
            $employee_id = $request->get('employee_id');
            $group_id = $request->get('kpi_group_id');
            $unallocation = $this->repository->unallocate($employee_id, $group_id);
            DB::commit();
            if ($unallocation) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                return response()->json($this->helperService->returnFalseResponse());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
