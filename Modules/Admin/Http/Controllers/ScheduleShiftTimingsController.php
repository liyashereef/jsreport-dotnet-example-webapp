<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ScheduleShiftTimingRequest;
use Modules\Admin\Repositories\ScheduleShiftTimingsRepository;

class ScheduleShiftTimingsController extends Controller
{
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\ScheduleShiftTimingsRepository $scheduleShiftTimingsRepository
     * @return void
     */
    public function __construct(ScheduleShiftTimingsRepository $scheduleShiftTimingsRepository, HelperService $helperService)
    {
        $this->repository = $scheduleShiftTimingsRepository;
        $this->helperService = $helperService;

    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('admin::masters.shift-timing');
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
    public function getSingle($shift_name)
    {
        return response()->json($this->repository->get($shift_name));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ScheduleShiftTimingRequest $request
     * @return json
     */
    public function store(ScheduleShiftTimingRequest $request)
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
