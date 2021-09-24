<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Repositories\WorkHourCustomerRepository;
use Modules\Timetracker\Models\EmployeeShiftWorkHourType;
use Modules\Admin\Models\CustomerType;
use Illuminate\Http\Request;

class WorkHourCustomerController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CandidateTerminationReasonLookupRepository $repository
     * @return void
     */
    public function __construct(WorkHourCustomerRepository $repository, HelperService $helperService)
    {
        $this->repository = $repository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workHourType = EmployeeShiftWorkHourType::orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        $customerType = CustomerType::orderBy('name', 'ASC')->pluck('name', 'id')->toArray();
        return view('admin::activitycode.work-hour-customer', compact('workHourType', 'customerType'));
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
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PositionRequest $request
     * @return json
     */
    public function store(Request $request)
    {
        $request->validate([
            'work_hour_type_id' => 'required',
            'customer_type_id' => 'required',
            'code' => 'required|regex:/^[_a-zA-Z0-9-\.\s]+$/u',
            'duplicate_code' => 'nullable|regex:/^[_a-zA-Z0-9-\.\s]+$/u',
            'description' => 'required|string'
        ]);

        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            dd($e);
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
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
