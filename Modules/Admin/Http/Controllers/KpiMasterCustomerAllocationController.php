<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\KpiMasterCustomerAllocationRepository;
use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;

class KpiMasterCustomerAllocationController extends Controller
{

    protected $repository;
    protected $helperService;

    public function __construct(
        KpiMasterCustomerAllocationRepository $kpiMasterCustomerAllocationRepository,
        HelperService $helperService
    ) {
        $this->repository = $kpiMasterCustomerAllocationRepository;
        $this->helperService = $helperService;
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kpi_master_id' => 'required|numeric',
            'customer_id' => 'required|numeric'
        ]);

        try {

            $inputs = $request->all();
            $inputs['created_by'] = Auth::id();

            if ($this->repository->checkAlreadyAllocated(
                $inputs['kpi_master_id'],
                $inputs['customer_id']
            )) {
                return response()->json([
                    "suceess" => false,
                    "message" => "KPI is already allocated"
                ]);
            }

            \DB::beginTransaction();
            $inputs['id'] = null;
            $result = $this->repository->store($inputs);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e->getMessage()));
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
            return response()->json([
                "success" => true,
                "message" => "KPI unallocated successfully"
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => "Something went wrong"
            ]);
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function getAllByCustomerId($customerId)
    {
        return datatables()->of($this->repository->getByCustomerId($customerId))
            ->addIndexColumn()
            ->toJson();
    }
}
