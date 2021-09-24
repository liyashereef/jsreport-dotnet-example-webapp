<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\TimeOffRequestTypeRequest;
use Modules\Admin\Repositories\TimeOffRequestTypeLookupRepository;

class TimeOffRequestTypeLookupController extends Controller
{

    /**
     * Repository instance.
     * @var \App\Repositories\TimeOffRequestTypeLookupRepository
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TimeOffRequestTypeLookupRepository $timeOffRequestTypeLookupRepository
     * @return void
     */
    public function __construct(TimeOffRequestTypeLookupRepository $timeOffRequestTypeLookupRepository, HelperService $helperService)
    {
        $this->repository = $timeOffRequestTypeLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     *  Load the Request Type Masters Page
     *
     * @return view
     */
    public function index()
    {
        return view('admin::masters.time-off-request-type');
    }

    /**
     *Get a listing of the Request Type Master for Datatable.
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function lookupList()
    {
        return $this->repository->getLookupList();
    }

    /**
     * Store a newly created Request Type in storage.
     *
     * @param  App\Http\Requests\TimeOffRequestTypeRequest $request
     * @return json
     */
    public function store(TimeOffRequestTypeRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            //$lookup->save();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     *Get details of single Request Type Master
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
