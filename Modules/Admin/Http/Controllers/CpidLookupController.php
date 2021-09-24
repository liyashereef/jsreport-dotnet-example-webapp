<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Http\Requests\CpidRequest;
use Modules\Admin\Repositories\CpidLookupRepository;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\Admin\Models\Positions;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;
use Modules\Admin\Repositories\CpidFunctionRepository;

class CpidLookupController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $positionLookupRepository;
    protected $cpidCustomerAllocationRepository;
    protected $cpidFunctionRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CpidLookupRepository $cpidLookupRepository
     * @return void
     */

    public function __construct(
        CpidLookupRepository $cpidLookupRepository,
        CpidFunctionRepository $cpidFunctionRepository,
        HelperService $helperService,
        PositionLookupRepository $positionLookupRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository
    ) {

        $this->repository = $cpidLookupRepository;
        $this->positionLookupRepository = $positionLookupRepository;
        $this->cpidCustomerAllocationRepository=$cpidCustomerAllocationRepository;
        $this->helperService = $helperService;
        $this->cpidFunctionRepository = $cpidFunctionRepository;
        
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions = $this->positionLookupRepository->getList();
        $cpidFunctions = $this->cpidFunctionRepository->getList();

        return view('admin::masters.cpid', [
            'positions' => $positions,
            'cpidFunctions' => $cpidFunctions
        ]);
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return json
     */
    public function store(CpidRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {  
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            //validate cpid dependencies
            $cpidDeps = $this->cpidCustomerAllocationRepository->getByCpid($id);
            if(count($cpidDeps) > 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Please remove all assigned cpids and try again'
                ]);
            }
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            if($lookup_delete){
                return response()->json($this->helperService->returnTrueResponse());
            }
            return response()->json($this->helperService->returnFalseResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function historyIndex($id)
    {
        $result=$this->repository->get($id);
        return view('admin::masters.cpid_history',compact('id','result'));
    }
    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function gethistoryList($id)
    {
        return datatables()->of($this->repository->getHistoryAll($id))->addIndexColumn()->toJson();
    }

    function checkCpidAllocation($cpid)
    {
        $result=$this->cpidCustomerAllocationRepository->check_cpid_allocated_or_not($cpid);
        return count($result);
    }
}
