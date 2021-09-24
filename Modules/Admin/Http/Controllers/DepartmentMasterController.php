<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\DepartmentMasterRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Http\Requests\DepartmentMappingRequest;
use Modules\Admin\Models\User;

class DepartmentMasterController extends Controller
{

    protected $helperService;

    public function __construct(
        DepartmentMasterRepository $repository,
        UserRepository $userRepository,
        HelperService $helperService
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $users = $this->userRepository->getUserLookup(null,['admin','super_admin'],null,true,null,true)
            ->orderBy('first_name', 'asc')
            ->get();
        $employeeName = User::where('active', 1)
        ->orderBy('first_name', 'ASC')
        ->get();
        return view('admin::masters.department-master',compact('users','employeeName'));
    }


    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    public function allocateEmployee($id){

    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(DepartmentMappingRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $delete = $this->repository->destroy($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }


}
