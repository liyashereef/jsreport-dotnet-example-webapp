<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\KpiCustomerHeaderRepository;
use Modules\Admin\Repositories\KpiMasterAllocationRepository;

class KpiCustomerHeaderController extends Controller
{
    protected $repository;
    protected $kpiMasterAllocationRepo;
    protected $helperService;

    public function __construct(
        KpiCustomerHeaderRepository $kpiCustomerHeaderRepository,
        KpiMasterAllocationRepository $kpiMasterAllocationRepository,
        HelperService $helperService
        )
    {
        $this->repository = $kpiCustomerHeaderRepository;
        $this->helperService = $helperService;
        $this->kpiMasterAllocationRepo = $kpiMasterAllocationRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::kpi.kpi-headers');
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
        $request->validate([
            'name' => 'required'
        ]);

        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            if($request->filled('id')){
                $inputs['updated_by'] = Auth::id();
            }else{
                $inputs['created_by'] = Auth::id();
            }
            // $inputs['is_active'] = $request->has('is_active') ? 1 : 0;
            $result = $this->repository->store($inputs);
            \DB::commit();
         return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            //Remove the header
            $this->repository->destroy($id);
            //Remove kpi header allocation
            $this->kpiMasterAllocationRepo->removeAllocationByHeader($id);
            \DB::commit();
            return response()->json([
                "success" => true,
                "message" => 'Header has been deleted successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => 'Something went wrong'
            ]);
        }

    }

    public function getById($id){
        return $this->repository->getById($id);
    }

    public function getAll(){
        return datatables()->of($this->repository->all())
        ->addIndexColumn()
        ->toJson();
    }

}
