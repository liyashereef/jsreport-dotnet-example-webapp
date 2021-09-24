<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Modules\Admin\Http\Requests\WorkTypeRequest;
use Modules\Admin\Repositories\WorkTypeRepository;

class WorkTypeController extends Controller
{
    protected $helperService, $repository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\WorkTypeRepository $workTypeRepository;
     * @return void
     */
    public function __construct(HelperService $helperService, WorkTypeRepository $workTypeRepository)
    {
        $this->helperService = $helperService;
        $this->repository = $workTypeRepository;
    }

    /**
     * Display a listing of the Work Types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin::masters.work-type');
    }

    /**
     * Store  newly created  Work Type in storage.
     *
     * @param  Modules\Admin\Http\Requests\WorkTypeRequest $request
     * @return Json
     */
    public function store(WorkTypeRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * List all Work Types in datatable.
     *
     *
     * @return Json
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();

    }

    /**
     * Show the form for editing the specified Work Type.
     *
     * @param  $id
     * @return Json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Remove the specified Work Type from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();
            $holiday_delete = $this->repository->deleteWorkType($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

}
