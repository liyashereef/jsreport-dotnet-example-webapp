<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\JobRequisitionReasonRequest;
use Modules\Admin\Repositories\JobRequisitionReasonLookupRepository;

class JobRequisitionReasonLookupController extends Controller
{

    /**
     * Repository instance.
     * @var \App\Repositories\JobRequisitionReasonLookupRepository
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\SecurityClearanceLookupRepository $securityClearanceLookupRepository
     * @return void
     */
    public function __construct(JobRequisitionReasonLookupRepository $jobRequisitionReasonLookupRepository, HelperService $helperService)
    {
        $this->repository = $jobRequisitionReasonLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Reason Masters Page
     *
     * @return view
     * 
     */
    public function index()
    {
        //job_requisition_reason_lookups:"Edit button disable for Job Requisition Reasons"
        $terminate_id = 12;
        $resignate_id = 11;
        return view('admin::masters.job-requisition-reason', compact('terminate_id','resignate_id'));
    }

    /**
     *Get a listing of the Reasons Master for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created Security Clearance in storage.
     *
     * @param  App\Http\Requests\SecurityClearanceRequest $request
     * @return json
     */
    public function store(JobRequisitionReasonRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            //$lookup->save();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     *Get details of single Reason Master
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

}
