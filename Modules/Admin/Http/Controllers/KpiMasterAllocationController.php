<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Admin\Models\KpiThresholdColor;
use Modules\Admin\Repositories\KpiMasterAllocationRepository;
use Modules\Admin\Repositories\KpiMasterThresholdRepository;
use Modules\Admin\Repositories\KpiCustomerHeaderRepository;
use Modules\Admin\Repositories\KpiMasterRepository;

class KpiMasterAllocationController extends Controller
{
    protected $repository;
    protected $kpiCustomerHeaderRepository;
    protected $kpiMasterAllocationRepository;
    protected $kpiMasterThresholdRepository;
    protected $helperService;

    public function __construct(
        KpiMasterAllocationRepository $kpiMasterAllocationRepository,
        KpiMasterThresholdRepository $kpiMasterThresholdRepository,
        KpiCustomerHeaderRepository $kpiCustomerHeaderRepository,
        KpiMasterRepository $kpiMasterRepository,
        HelperService $helperService
    ) {
        $this->repository = $kpiMasterAllocationRepository;
        $this->kpiMasterThresholdRepository = $kpiMasterThresholdRepository;
        $this->kpiCustomerHeaderRepository = $kpiCustomerHeaderRepository;
        $this->kpiMasterRepository = $kpiMasterRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::kpi.kpi-header-allocation');
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
            'kpi_master_id' => 'required|integer',
            'kpi_customer_header_id' => 'required|integer',
            'min.*' => 'required',
            'max.*' => 'required',
        ]);

        //Check already allocated
        if ($this->repository->checkAlreadyAllocated(
            $request->input('kpi_master_id'),
            $request->input('kpi_customer_header_id'),
            $request->input('id')
        )) {
            return response()->json($this->helperService->returnFalseResponse('Header - Dictionary  already allocated'));
        }

        //Begin allocation
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            if ($request->filled('id')) {
                $inputs['updated_by'] = \Auth::id();
            } else {
                $inputs['created_by'] = \Auth::id();
            }
            unset($inputs['color_id']);
            unset($inputs['max']);
            unset($inputs['min']);
            //Store allocation
            $result = $this->repository->store($inputs);
            if ($result) {
                $colorIds = $request->input('color_id');
                $minLists = $request->input('min');
                $maxLists = $request->input('max');
                $thresholdIds = $request->input('threshold_id');

                if ($request->filled('threshold_id')) {
                    $thresholdInputs['updated_by'] = \Auth::id();
                } else {
                    $thresholdInputs['created_by'] = \Auth::id();
                }

                $thresholdInputs['is_active'] = 1;
                $thresholdInputs['kpi_master_allocation_id'] = $result->id;

                foreach ($colorIds as $key => $id) {
                    $thresholdInputs['kpi_threshold_color_id'] = $id;
                    $thresholdInputs['min'] = $minLists[$key];
                    $thresholdInputs['max'] = $maxLists[$key];
                    $thresholdInputs['id'] = $thresholdIds[$key];
                    //Store thresholds
                    $this->kpiMasterThresholdRepository->store($thresholdInputs);
                }
            }

            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
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

    public function getAll()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    public function getKpiAllocationSettings()
    {
        $result['headers'] = $this->kpiCustomerHeaderRepository->allActive();
        $result['colors'] = KpiThresholdColor::select('id', 'color', 'color_code')->get();
        return $result;
    }

    public function getUnallocatedKpis($headerId = null)
    {
        return $this->kpiMasterRepository->getAll();
    }
}
