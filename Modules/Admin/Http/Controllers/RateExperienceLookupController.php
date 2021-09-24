<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\RateExperienceLookupRequest;
use Modules\Admin\Repositories\RateExperienceLookupRepository;

class RateExperienceLookupController extends Controller
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
    public function __construct(RateExperienceLookupRepository $rateExperienceLookupRepository, HelperService $helperService)
    {
        $this->repository = $rateExperienceLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Reason Masters Page
     *
     * @return view
     */
    public function index()
    {
        return view('admin::masters.rate-experience-lookup');
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
    public function store(RateExperienceLookupRequest $request)
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
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

}
