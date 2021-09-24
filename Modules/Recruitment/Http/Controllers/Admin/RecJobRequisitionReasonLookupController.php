<?php

namespace Modules\Recruitment\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Http\Requests\RecJobRequisitionReasonRequest;
use Modules\Recruitment\Repositories\RecJobRequisitionReasonLookupRepository;

class RecJobRequisitionReasonLookupController extends Controller
{
    /**
     * Repository instance.
     * @var Modules\Recruitment\Repositories\RecJobRequisitionReasonLookupRepository
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\SecurityClearanceLookupRepository $securityClearanceLookupRepository
     * @return void
     */
    public function __construct(RecJobRequisitionReasonLookupRepository $recJobRequisitionReasonLookupRepository, HelperService $helperService)
    {
        $this->repository = $recJobRequisitionReasonLookupRepository;
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
        return view('recruitment::masters.job-requisition-reason', compact('terminate_id','resignate_id'));
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
    public function store(RecJobRequisitionReasonRequest $request)
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
