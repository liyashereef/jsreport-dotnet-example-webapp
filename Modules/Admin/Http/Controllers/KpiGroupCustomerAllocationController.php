<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\KpiGroupCustomerAllocationRepository;

class KpiGroupCustomerAllocationController extends Controller
{
    protected $repository;
    protected $helperService;

    public function __construct(
        KpiGroupCustomerAllocationRepository $kpiGroupCustomerAllocationRepository,
        HelperService $helperService
    ) {
        $this->repository = $kpiGroupCustomerAllocationRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('admin::index');
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
            'kpi_group_id' => 'required',
            'customer_id' => 'required'
        ]);

        try {
            $inputs = $request->all();
            $inputs['created_by'] = Auth::id();

            if ($this->repository->checkAlreadyAllocated(
                $inputs['customer_id'],
                $inputs['kpi_group_id']
            )) {
                return response()->json([
                    "suceess" => false,
                    "message" => "Group is already allocated"
                ]);
            }

            \DB::beginTransaction();
            $result = $this->repository->store($inputs);
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
            return response()->json([
                "success" => true,
                "message" => "Group unallocated successfully"
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
