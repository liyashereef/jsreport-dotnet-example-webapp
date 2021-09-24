<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\HelperService;
use DB;
use Modules\Admin\Http\Requests\RolesPermissionRequest;
use Modules\Admin\Repositories\RolesAndPermissionRepository;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Services\HelperService
     * @var \Modules\Admin\Repositories\RolesAndPermissionRepository;
     */
    protected $helperService, $rolesAndPermissionRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\RolesAndPermissionRepository $rolesAndPermissionRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, RolesAndPermissionRepository $rolesAndPermissionRepository)
    {
        $this->helperService = $helperService;
        $this->rolesAndPermissionRepository = $rolesAndPermissionRepository;
    }

    /**
     * Load the Roles and Permissions Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::permissions.role-permissions');
    }

    /**
     *Get a listing of the Roles for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->rolesAndPermissionRepository->getRoleList())->addIndexColumn()->toJson();
    }

    /**
     * Add or Edit Roles and Permissions Page
     *
     * @return \Illuminate\Http\Response
     */
    public function addOrEdit($id = null)
    {
        $data = $this->rolesAndPermissionRepository->addEdit($id);

        $existing_roles = $this->rolesAndPermissionRepository->defaultRolesArray();
        $basePermissions = $this->rolesAndPermissionRepository->getBasePermissionAsRoleArray();

        return view('admin::permissions.add-edit-permissions', [
            "data" => $data,
            "existing_roles" => $existing_roles,
            "basePermissions" => $basePermissions
        ]);
    }

    /**
     *Store Permission for specific Role.
     *
     * @return Json
     */
    public function store(RolesPermissionRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->rolesAndPermissionRepository->save($request);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     *Delete Roles and their Permission.
     *
     * @return Json
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $response = $this->rolesAndPermissionRepository->destroyRole($id);
            DB::commit();
            if ($response == true) {
                return response()->json($this->helperService->returnTrueResponse());
            } else {
                $response = ["success" => false, "message" => "Users with role exist"];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
