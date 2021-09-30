<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\CapacityToolTaskFrequencyRequest;
use Modules\Admin\Repositories\CapacityToolTaskFrequencyLookupRepository;

class CapacityToolTaskFrequencyLookupController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CapacityToolTaskFrequencyLookupRepository $capacityToolTaskFrequencyLookupRepository
     * @return void
     */
    public function __construct(CapacityToolTaskFrequencyLookupRepository $capacityToolTaskFrequencyLookupRepository, HelperService $helperService)
    {
        $this->repository = $capacityToolTaskFrequencyLookupRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::capacity-tool.task-frequency');
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
    public function store(CapacityToolTaskFrequencyRequest $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->repository->save($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
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
            $lookup_delete = $this->repository->delete($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}