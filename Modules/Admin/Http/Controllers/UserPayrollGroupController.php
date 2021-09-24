<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\UserPayrollGroupRepository;

class UserPayrollGroupController extends Controller
{
    public function __construct(
        UserPayrollGroupRepository $userPayrollGroupRepository,
        HelperService $helperService
    ) {
        $this->repository = $userPayrollGroupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::user.user-payroll-group');
    }

    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $id = request('id');
        $request->validate([
            'name' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:255|unique:user_payroll_groups,name,{$id},id,deleted_at,NULL",
            'apogee_code' => "bail|required|regex:/^[a-zA-Z0-9 .\-\)\(\[\]]*$/|max:255|unique:user_payroll_groups,apogee_code,{$id},id,deleted_at,NULL"
        ]);

        try {
            DB::beginTransaction();
            $inputs = $request->all();
            if ($request->filled('id')) {
                $inputs['updated_by'] = Auth::id();
            } else {
                $inputs['created_by'] = Auth::id();
            }

            $result = $this->repository->store($inputs);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $this->repository->destroy($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }
}
