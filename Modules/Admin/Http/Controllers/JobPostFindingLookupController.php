<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\JobPostFindingRequest;
use Modules\Admin\Http\Requests\SecurityClearanceRequest;
use Modules\Admin\Repositories\JobPostFindingLookupRepository;
use Modules\Admin\Repositories\SecurityClearanceLookupRepository;

class JobPostFindingLookupController extends Controller
{

    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param JobPostFindingLookupRepository $jobPostFindingLookupRepository
     * @param HelperService $helperService
     */
    public function __construct(
        JobPostFindingLookupRepository $jobPostFindingLookupRepository,
        HelperService $helperService
    )
    {
        $this->repository = $jobPostFindingLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the Job Post Finding  Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.job-post-finding');
    }

    /**
     * Display a listing of Job Post Finding.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single Job Post Finding
     *
     * @param $id
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Store a newly created Job Post Finding in storage.
     *
     * @param JobPostFindingRequest $request
     * @return json
     */
    public function store(JobPostFindingRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified Job Post Finding from storage.
     *
     * @param $id
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
