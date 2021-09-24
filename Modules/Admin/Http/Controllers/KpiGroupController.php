<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\KpiGroup;
use Modules\Admin\Repositories\KpiGroupRepository;
use Modules\Admin\Repositories\KpiMasterRepository;

class KpiGroupController extends Controller
{
    protected $repository;
    protected $helperService;

    public function __construct(
        KpiGroupRepository $kpiGroupRepository,
        HelperService $helperService
    ) {
        $this->repository = $kpiGroupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::kpi.kpi-groups');
    }

    public function getList()
    {
        return datatables()->of($this->repository->getAll())
            ->addColumn('active_fld', function (KpiGroup $kg) {
                return $kg->is_active == 1 ? 'Active' : 'Inactive';
            })
            ->rawColumns(['active_fld'])
            ->addIndexColumn()
            ->toJson();
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
        $id = $request->input('id');
        $request->validate([
            'name' => 'required|unique:kpi_groups,name,' . $id . ',id,deleted_at,NULL',
            'parent_id' => 'nullable|numeric|exists:kpi_groups,id'
        ]);

        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            if ($request->filled('id')) {
                $inputs['updated_by'] = Auth::id();
            } else {
                $inputs['created_by'] = Auth::id();
            }
            $inputs['is_active'] = $request->has('is_active') ? 1 : 0;

            $result = $this->repository->store($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();

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
            \DB::beginTransaction();
            //Check for group allocation
            if ($this->repository->checkForAllocation($id)) {
                return response()->json([
                    "success" => false,
                    "message" => "Group allocation exists.Please unallocate and try again"
                ]);
            }
            if ($this->repository->checkForCustomerAllocation($id)) {
                return response()->json([
                    "success" => false,
                    "message" => "Customer allocation exists.Please unallocate and try again"
                ]);
            }
            $this->repository->destroy($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function getAllLeafNodes()
    {
        return $this->repository->getAllLeafNodes();
    }

    public function getAllParentNodes()
    {
        return $this->repository->getAllParentNodes();
    }
}
