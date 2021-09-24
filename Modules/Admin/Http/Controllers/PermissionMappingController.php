<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Repositories\PermissionMappingRepository;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Modules\Admin\Models\PermissionMapping;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionMappingController extends Controller
{

    /**
     * Repository instance.
     * @var \App\Repositories\CandidateBrandAwarenessRepository
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CandidateBrandAwarenessRepository $candidateBrandAwarenessRepository
     * @return void
     */
    public function __construct(PermissionMappingRepository $permissionMappingRepository, RolesAndPermissionRepository $rolesAndPermissionRepository, HelperService $helperService)
    {
        $this->repository = $permissionMappingRepository;
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Request Type Masters Page
     *
     * @return view
     */
    public function index()
    {
        $role_arr=$this->rolesAndPermissionRepository->getRoleList()->where('name', '!=', 'admin')->pluck('name', 'id')->toArray();
        $roles=array_map(function ($role_arr) {
               return ucwords(str_replace("_", " ", $role_arr));
        }, $role_arr);
        $permission_slug= $this->rolesAndPermissionRepository->getBasePermissionAsRoleArraySlugs();
        $permission_arr=Permission::whereIn('name', $permission_slug)->orderBy('name')->pluck('name', 'id')->toArray();
        $permissions=array_map(function ($permission_arr) {
               return ucwords(str_replace("_", " ", $permission_arr));
        }, $permission_arr);
        return view('admin::masters.permission-mapping', compact('roles', 'permissions'));
    }
   
    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function lookupList()
    {
        return $this->repository->getLookupList();
    }

    /**
     * Store a newly created Brand Awareness in storage.
     *
     * @param  App\Http\Requests\CandidateBrandAwarenessRequest $request
     * @return json
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     *Get details of single Brand awareness Master
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     *Get details of single Brand awareness Master
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json
     */
    public function edit($role_id)
    {
        return response()->json($this->repository->getPermissionBasedOnRole($role_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
